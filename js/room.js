import * as Y from 'yjs';
import { EditorState } from '@codemirror/state';
import { EditorView, keymap, lineNumbers } from '@codemirror/view';
import { defaultKeymap } from '@codemirror/commands';
import { yCollab } from 'y-codemirror.next';
import { basicSetup } from 'codemirror';
import { markdown } from '@codemirror/lang-markdown';
import { MockWebSocket } from './mock-socket.js';

import { SimpleWebRTC } from './simple-webrtc.js';

// Polyfill WebSocket for signaling to use our PHP backend
const RealWebSocket = window.WebSocket;
window.WebSocket = MockWebSocket;

const roomKey = document.getElementById('room-key').innerText;
const editorContainer = document.getElementById('editor-container');
const connectionStatus = document.getElementById('connection-status');
const activeUsersCount = document.getElementById('active-users-count');
const roomNameEl = document.getElementById('room-name');

// Generate a random color for this user
const userColor = '#' + Math.floor(Math.random() * 16777215).toString(16);

// Fetch Room Details & User Info
async function init() {
    try {
        const response = await fetch(`./api/room/details?key=${roomKey}`);
        if (!response.ok) {
            throw new Error('Failed to join room');
        }
        const data = await response.json();

        roomNameEl.innerText = data.room.name;

        // Load initial content from DB persistence
        const docRes = await fetch(`./api/document?action=get&room_key=${roomKey}`);
        const docData = await docRes.json();
        const initialContent = docData.content || '';

        // Prepare User Object for WebRTC
        // API returns { id, username, role, is_guest? }
        // SimpleWebRTC expects { name, color, id }
        const rtcUser = {
            id: data.me.id,
            name: data.me.username, // Map username to name
            color: userColor,       // Use the locally generated color
            isGuest: !!data.me.is_guest
        };

        setupCollaboration(data.room.room_key, rtcUser, initialContent);

    } catch (e) {
        console.error(e);
        roomNameEl.innerText = 'Error loading room';
        connectionStatus.innerText = 'Error';
        connectionStatus.parentElement.classList.add('text-red-600');
    }
}

function setupCollaboration(roomName, user, initialContent) {
    const ydoc = new Y.Doc();
    const ytext = ydoc.getText('codemirror');

    // Initialize doc with DB content if YDocs are empty
    if (initialContent && ytext.toString() === '') {
        ytext.insert(0, initialContent);
    }

    // Use our Custom Manual WebRTC Provider
    const rtc = new SimpleWebRTC(roomName, ydoc, user);

    // --- Video Call Logic ---
    const videoContainer = document.getElementById('video-container'); // The sidebar
    const localVideoWrapper = document.getElementById('local-video-wrapper');
    const localVideo = document.getElementById('local-video');
    const joinCallBtn = document.getElementById('join-call-btn');
    const endCallBtn = document.getElementById('end-call-btn');
    const toggleVideoBtn = document.getElementById('toggle-video-btn');
    const toggleAudioBtn = document.getElementById('toggle-audio-btn');
    const videoStreams = document.getElementById('video-streams');

    let videoEnabled = true;
    let audioEnabled = true;

    // Helper to check sidebar visibility
    function updateSidebarVisibility() {
        const hasLocal = localVideo.srcObject && localVideo.srcObject.active;
        const hasRemote = videoStreams.children.length > 0;

        if (hasLocal || hasRemote) {
            videoContainer.classList.remove('hidden');
            videoContainer.classList.add('flex'); // Ensure flex is on when active
        } else {
            videoContainer.classList.add('hidden');
            videoContainer.classList.remove('flex');
        }
    }

    joinCallBtn.addEventListener('click', async () => {
        try {
            const stream = await rtc.enableVideo();
            localVideo.srcObject = stream;
            localVideoWrapper.classList.remove('hidden');

            joinCallBtn.classList.add('hidden');
            endCallBtn.classList.remove('hidden');

            updateSidebarVisibility();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'Could not access camera/microphone. Please check permissions.'
            });
            console.error(e);
        }
    });

    endCallBtn.addEventListener('click', () => {
        rtc.disableVideo();
        localVideo.srcObject = null;
        localVideoWrapper.classList.add('hidden');

        endCallBtn.classList.add('hidden');
        joinCallBtn.classList.remove('hidden');

        // Reset toggles
        videoEnabled = true;
        audioEnabled = true;

        updateSidebarVisibility();
    });

    toggleVideoBtn.addEventListener('click', () => {
        videoEnabled = !videoEnabled;
        rtc.toggleVideo(videoEnabled);
        toggleVideoBtn.innerHTML = videoEnabled ? '<i class="fas fa-video"></i>' : '<i class="fas fa-video-slash"></i>';
        toggleVideoBtn.className = `p-1 rounded text-white text-xs ${videoEnabled ? 'bg-gray-700/80 hover:bg-gray-600' : 'bg-red-600/80 hover:bg-red-500'}`;
    });

    toggleAudioBtn.addEventListener('click', () => {
        audioEnabled = !audioEnabled;
        rtc.toggleAudio(audioEnabled);
        toggleAudioBtn.innerHTML = audioEnabled ? '<i class="fas fa-microphone"></i>' : '<i class="fas fa-microphone-slash"></i>';
        toggleAudioBtn.className = `p-1 rounded text-white text-xs ${audioEnabled ? 'bg-gray-700/80 hover:bg-gray-600' : 'bg-red-600/80 hover:bg-red-500'}`;
    });

    rtc.onStreamAdded = (peerId, stream) => {
        console.log('[Room] Stream added for', peerId);
        // Check if video element already exists
        let vidContainer = document.getElementById(`container-${peerId}`);
        if (!vidContainer) {
            vidContainer = document.createElement('div');
            vidContainer.className = 'relative w-full aspect-video bg-black rounded-lg overflow-hidden shadow-sm';
            vidContainer.id = `container-${peerId}`;

            const vid = document.createElement('video');
            vid.id = `video-${peerId}`;
            vid.className = 'w-full h-full object-cover';
            vid.autoplay = true;
            vid.playsInline = true;

            const label = document.createElement('div');
            label.className = 'absolute bottom-1 left-1 text-[10px] text-white bg-black/50 px-1 rounded';
            // Try to find user name from awareness
            const peerState = Array.from(rtc.awareness.getStates().values()).find(s => s.user.id === peerId) || {};
            // If peerId is random (SimpleWebRTC default), we might not match user.id. 
            // Fallback to generic if not found or improve mapping later.
            label.innerText = peerState.user ? peerState.user.name : 'Remote User';

            vidContainer.appendChild(vid);
            vidContainer.appendChild(label);
            videoStreams.appendChild(vidContainer);

            // Handle stream ending (removal)
            stream.onremovetrack = () => {
                console.log('[Room] Stream track removed for', peerId);
                // If no tracks left, remove container?
                if (stream.getTracks().length === 0) {
                    vidContainer.remove();
                    updateSidebarVisibility();
                }
            };

            // Also listen to specific track ended event (standard for stop())
            stream.getTracks().forEach(track => {
                track.onended = () => {
                    console.log('[Room] Track ended for', peerId);
                    if (stream.getTracks().every(t => t.readyState === 'ended')) {
                        vidContainer.remove();
                        updateSidebarVisibility();
                    }
                };
            });
        } else {
            const vid = vidContainer.querySelector('video');
            vid.srcObject = stream;
        }

        updateSidebarVisibility();
    };
    const undoManager = new Y.UndoManager(ytext);

    // Note: SimpleWebRTC doesn't have built-in Awareness (Cursors) yet in this simple version.
    // We strictly focused on Text Sync + Persistence.

    connectionStatus.innerText = 'Active'; // SimpleWebRTC is active immediately

    const state = EditorState.create({
        doc: ytext.toString(), // Start with Yjs text (which has initialContent)
        extensions: [
            basicSetup,
            markdown(),
            keymap.of(defaultKeymap),
            // Bind CodeMirror to Yjs
            yCollab(ytext, rtc.awareness, { undoManager }), // Pass null for awareness for now
            EditorView.theme({
                "&": { height: "100%" },
                ".cm-scroller": { overflow: "auto" }
            })
        ]
    });

    const view = new EditorView({
        state,
        parent: editorContainer
    });

    // Update Users Count and List
    rtc.awareness.on('change', () => {
        const states = Array.from(rtc.awareness.getStates().values());
        activeUsersCount.innerText = states.length;

        // Update user list UI if needed
        renderUserList(states);
    });

    // Persistence: Save to DB every 10 seconds
    setInterval(async () => {
        const content = ytext.toString();
        // Optimize: Only save if not empty (or check dirty flag if complex)
        await fetch(`./api/document?action=save`, {
            method: 'POST',
            body: JSON.stringify({
                room_key: roomName,
                content: content
            })
        });

    }, 10000);
}

function renderUserList(states) {
    const usersList = document.getElementById('users-list');
    usersList.innerHTML = '';

    states.forEach(state => {
        if (state.user && state.user.name) {
            const avatar = document.createElement('div');
            avatar.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs text-white font-bold ring-2 ring-white dark:ring-gray-800';
            avatar.style.backgroundColor = state.user.color || '#888';
            avatar.innerText = state.user.name.substring(0, 2).toUpperCase();
            avatar.title = state.user.name;
            usersList.appendChild(avatar);
        }
    });
}

document.getElementById('copy-link-btn').addEventListener('click', () => {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Link copied!',
            text: 'Room link copied to clipboard.',
            timer: 2000,
            showConfirmButton: false
        });
    });
});

init();
