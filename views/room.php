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
            </div>
        </div>
    </div>

    <!-- Toolbar/Actions -->
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
    <!-- Editor Container -->
    <div id="editor-container"
        class="flex-grow border-x border-b border-gray-200 dark:border-gray-700 rounded-b-lg overflow-hidden bg-white dark:bg-gray-900 text-base">
        <!-- CodeMirror will be mounted here -->
    </div>
</div>

<script type="module" src="./js/room.js"></script>
<style>
    /* Custom editor styles */
    .cm-editor {
        height: 100%;
    }

    .cm-scroller {
        overflow: auto;
    }

    /* User cursor styles */
    .yRemoteSelection {
        background-color: rgba(250, 129, 0, .5);
    }

    .yRemoteSelectionHead {
        position: absolute;
        border-left: orange solid 2px;
        border-top: orange solid 2px;
        border-bottom: orange solid 2px;
        height: 100%;
        box-sizing: border-box;
    }

    .yRemoteSelectionHead::after {
        position: absolute;
        content: ' ';
        border: 3px solid orange;
        border-radius: 4px;
        left: -4px;
        top: -5px;
    }
</style>