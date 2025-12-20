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

        setupCollaboration(data.room.room_key, data.me, initialContent);

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
        alert('Link copied to clipboard!');
    });
});

init();
