<<<<<<< HEAD
<div class="h-[calc(100vh-10rem)] flex flex-col animate-fade-in">
    <!-- Room Header -->
    <div class="glass-card rounded-2xl p-4 mb-4">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white break-all" id="room-name">Loading Room...
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <span>Room Key:</span>
                        <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-lg select-all"
                            id="room-key"><?= htmlspecialchars($_GET['key']) ?></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full lg:w-auto justify-between lg:justify-end">
                <div class="flex items-center gap-2 text-sm">
                    <span class="flex items-center gap-2 text-green-600 dark:text-green-400">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span id="connection-status">Connecting...</span>
                    </span>
                </div>
                <div id="users-list" class="flex -space-x-2 overflow-hidden">
                    <!-- User avatars will go here -->
                </div>
                <button id="copy-link-btn"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium transition-all hover:shadow-md">
                    <i class="fas fa-link"></i>
                    <span class="hidden sm:inline">Copy Invite Link</span>
                </button>
=======
<div class="h-[calc(100vh-12rem)] flex flex-col">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white" id="room-name">Loading Room...</h1>
            <p class="text-sm text-gray-500">Room Key: <span
                    class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded select-all"
                    id="room-key"><?= htmlspecialchars($_GET['key']) ?></span></p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span id="connection-status">Connecting...</span>
            </span>
            <div id="users-list" class="flex -space-x-2 overflow-hidden">
                <!-- User avatars will go here -->
>>>>>>> origin/main
            </div>
        </div>
    </div>

    <!-- Toolbar/Actions -->
<<<<<<< HEAD
    <div class="toolbar-glass rounded-xl p-3 mb-2 flex justify-between items-center">
        <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
            <div class="flex items-center gap-2">
                <i class="fas fa-users text-primary-500"></i>
                <span><span id="active-users-count">0</span> active</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 hidden sm:block">Real-time collaboration powered by WebRTC</span>
        </div>
    </div>

    <?php
    // We can check role here via PHP if we want server-side button rendering, 
    // but JS will handle client-side logic too.
    ?>
=======
    <div
        class="bg-gray-50 dark:bg-gray-800 p-2 rounded-t-lg border border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            <span id="active-users-count">0</span> active users
        </div>

        <?php
        // We can check role here via PHP if we want server-side button rendering, 
        // but JS will handle client-side logic too.
        ?>
        <button id="copy-link-btn"
            class="text-xs bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-3 py-1 rounded transition">
            <i class="fas fa-link mr-1"></i> Copy Invite Link
        </button>
    </div>
>>>>>>> origin/main

    <!-- Import Map to resolve One Version of Truth for dependencies -->
    <script type="importmap">
    {
        "imports": {
            "yjs": "https://esm.sh/yjs@13.6.14",
            "y-webrtc": "https://esm.sh/y-webrtc@10.3.0?external=yjs",
            "@codemirror/state": "https://esm.sh/@codemirror/state@6.4.1",
            "@codemirror/view": "https://esm.sh/@codemirror/view@6.26.0?external=@codemirror/state",
            "@codemirror/commands": "https://esm.sh/@codemirror/commands@6.5.0?external=@codemirror/state,@codemirror/view",
            "@codemirror/lang-markdown": "https://esm.sh/@codemirror/lang-markdown@6.2.0?external=@codemirror/state,@codemirror/view",
            "@codemirror/language": "https://esm.sh/@codemirror/language@6.10.1?external=@codemirror/state",
            "y-codemirror.next": "https://esm.sh/y-codemirror.next@0.3.5?external=yjs,@codemirror/state,@codemirror/view",
            "y-protocols/awareness": "https://esm.sh/y-protocols@1.0.6/awareness?external=yjs",
            "codemirror": "https://esm.sh/codemirror@6.0.1?external=@codemirror/state,@codemirror/view"
        }
    }
    </script>
<<<<<<< HEAD

    <!-- Main Content Area -->
    <div
        class="flex-grow flex flex-col lg:flex-row overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
        <!-- Editor Container -->
        <div id="editor-container"
            class="flex-grow bg-white dark:bg-gray-900 text-base relative h-1/2 lg:h-auto min-h-[300px]">
            <!-- CodeMirror will be mounted here -->
        </div>

        <!-- Video Sidebar -->
        <div id="video-container"
            class="hidden lg:flex w-full lg:w-72 bg-gray-50 dark:bg-gray-800/50 p-4 overflow-y-auto border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 flex-shrink-0 h-1/2 lg:h-auto flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-video text-primary-500"></i>Video Call
                </h3>
            </div>
            <div class="flex flex-col gap-3" id="video-streams">
                <!-- Local Video -->
                <div class="relative w-full aspect-video bg-gray-900 rounded-xl overflow-hidden shadow-lg group hidden"
                    id="local-video-wrapper">
                    <video id="local-video" class="w-full h-full object-cover transform scale-x-[-1]" autoplay muted
                        playsinline></video>
                    <div
                        class="absolute bottom-2 left-2 text-xs text-white bg-black/60 backdrop-blur px-2 py-1 rounded-lg flex items-center gap-1">
                        <i class="fas fa-user text-xs"></i>You
                    </div>
                    <div
                        class="absolute bottom-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button id="toggle-video-btn"
                            class="p-2 rounded-lg bg-gray-800/80 hover:bg-gray-700 text-white text-xs transition-all"
                            title="Toggle Camera">
                            <i class="fas fa-video"></i>
                        </button>
                        <button id="toggle-audio-btn"
                            class="p-2 rounded-lg bg-gray-800/80 hover:bg-gray-700 text-white text-xs transition-all"
                            title="Toggle Mic">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                </div>
                <!-- Remote videos will be appended here -->
            </div>
        </div>
    </div>
</div>

<!-- Video Call Controls (Floating) -->
<div class="fixed bottom-6 right-6 flex flex-col gap-3 z-40">
    <button id="join-call-btn"
        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-full shadow-lg transition-all transform hover:scale-105 hover:shadow-xl font-medium">
        <i class="fas fa-video"></i>
        <span>Join Call</span>
    </button>
    <button id="end-call-btn"
        class="hidden flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-full shadow-lg transition-all transform hover:scale-105 hover:shadow-xl font-medium">
        <i class="fas fa-phone-slash"></i>
        <span>End Call</span>
    </button>
</div>

<script type="module" src="./js/room.js"></script>

=======
    <!-- Editor Container -->
    <div id="editor-container"
        class="flex-grow border-x border-b border-gray-200 dark:border-gray-700 rounded-b-lg overflow-hidden bg-white dark:bg-gray-900 text-base">
        <!-- CodeMirror will be mounted here -->
    </div>
</div>

<script type="module" src="./js/room.js"></script>
>>>>>>> origin/main
<style>
    /* Custom editor styles */
    .cm-editor {
        height: 100%;
<<<<<<< HEAD
        font-size: 15px;
=======
>>>>>>> origin/main
    }

    .cm-scroller {
        overflow: auto;
<<<<<<< HEAD
        padding: 16px;
    }

    .cm-content {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .cm-gutters {
        background: transparent;
        border-right: 1px solid rgba(156, 163, 175, 0.2);
=======
>>>>>>> origin/main
    }

    /* User cursor styles */
    .yRemoteSelection {
<<<<<<< HEAD
        background-color: rgba(99, 102, 241, 0.3);
=======
        background-color: rgba(250, 129, 0, .5);
>>>>>>> origin/main
    }

    .yRemoteSelectionHead {
        position: absolute;
<<<<<<< HEAD
        border-left: #6366f1 solid 2px;
        border-top: #6366f1 solid 2px;
        border-bottom: #6366f1 solid 2px;
=======
        border-left: orange solid 2px;
        border-top: orange solid 2px;
        border-bottom: orange solid 2px;
>>>>>>> origin/main
        height: 100%;
        box-sizing: border-box;
    }

    .yRemoteSelectionHead::after {
        position: absolute;
        content: ' ';
<<<<<<< HEAD
        border: 3px solid #6366f1;
=======
        border: 3px solid orange;
>>>>>>> origin/main
        border-radius: 4px;
        left: -4px;
        top: -5px;
    }
<<<<<<< HEAD

    /* Dark mode editor */
    .dark .cm-editor {
        background: #111827;
    }

    .dark .cm-gutters {
        background: transparent;
        border-right-color: rgba(75, 85, 99, 0.3);
    }

    /* Scrollbar styling */
    .cm-scroller::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .cm-scroller::-webkit-scrollbar-track {
        background: transparent;
    }

    .cm-scroller::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.4);
        border-radius: 4px;
    }

    .cm-scroller::-webkit-scrollbar-thumb:hover {
        background: rgba(156, 163, 175, 0.6);
    }
=======
>>>>>>> origin/main
</style>