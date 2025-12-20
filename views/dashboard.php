<?php if (isset($_GET['error'])): ?>
    <div class="glass-card bg-red-50/80 dark:bg-red-900/30 border-l-4 border-red-500 px-5 py-4 rounded-xl mb-6 animate-scale-in"
        role="alert">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
            <span class="text-red-700 dark:text-red-300"><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    </div>
<?php endif; ?>

<!-- Action Cards Grid -->
<div class="grid md:grid-cols-2 gap-6 mb-10">
    <!-- Join Room Card -->
    <div class="glass-card rounded-2xl p-6 hover-lift group">
        <div class="flex items-center gap-4 mb-5">
            <div
                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/40 transition-all duration-300 group-hover:scale-110">
                <i class="fas fa-sign-in-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Join Existing Room</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Enter a room key to collaborate</p>
            </div>
        </div>
        <form action="./join-room" method="POST">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="room_key" placeholder="Enter Room Key" required class="modern-input flex-1">
                <button type="submit" class="btn-modern whitespace-nowrap">
                    <i class="fas fa-arrow-right mr-2"></i>Join Room
                </button>
            </div>
        </form>
    </div>

    <!-- Create Room Card -->
    <div class="glass-card rounded-2xl p-6 hover-lift group">
        <div class="flex items-center gap-4 mb-5">
            <div
                class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg group-hover:shadow-green-500/40 transition-all duration-300 group-hover:scale-110">
                <i class="fas fa-plus text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Create New Room</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Start a new collaboration space</p>
            </div>
        </div>
        <form action="./create-room" method="POST">
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                <input type="text" name="name" placeholder="Room Name (e.g. Project X)" required
                    class="modern-input flex-1">
                <label
                    class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer hover:text-primary-600 transition-colors px-3">
                    <input type="checkbox" name="is_public"
                        class="w-5 h-5 rounded-lg border-2 border-gray-300 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 cursor-pointer">
                    <span>Public</span>
                </label>
                <button type="submit" class="btn-modern whitespace-nowrap"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-rocket mr-2"></i>Create
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Section -->
<div>
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Rooms</h2>
        <span class="badge-gradient"><?= count($myRooms) + count($sharedRooms) ?> total</span>
    </div>

    <?php if (empty($myRooms) && empty($sharedRooms)): ?>
        <div class="glass-card rounded-2xl p-12 text-center">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-folder-open text-gray-400 dark:text-gray-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No rooms yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Create your first room or join an existing one to get
                started.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="#" onclick="document.querySelector('input[name=name]').focus(); return false;" class="btn-modern"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-plus mr-2"></i>Create Room
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($myRooms as $room): ?>
                <div class="glass-card rounded-2xl p-5 hover-lift group border-l-4 border-primary-500">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-bold text-lg text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <?= htmlspecialchars($room['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md select-all">
                                    <?= htmlspecialchars($room['room_key']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="flex gap-1.5">
                            <?php if (!empty($room['is_public'])): ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <i class="fas fa-globe text-xs mr-1"></i>Public
                                </span>
                            <?php endif; ?>
                            <span class="badge-gradient">Owner</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="./room?key=<?= htmlspecialchars($room['room_key']) ?>"
                            class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold transition-all group-hover:gap-3">
                            Open Room <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </a>
                        <form action="./delete-room" method="POST" class="delete-room-form">
                            <input type="hidden" name="room_key" value="<?= htmlspecialchars($room['room_key']) ?>">
                            <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">
                            <button type="submit"
                                class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                title="Delete Room">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($sharedRooms as $room): ?>
                <div class="glass-card rounded-2xl p-5 hover-lift group border-l-4 border-gray-400 dark:border-gray-600">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-bold text-lg text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <?= htmlspecialchars($room['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md select-all">
                                    <?= htmlspecialchars($room['room_key']) ?>
                                </span>
                            </p>
                        </div>
                        <div class="flex gap-1.5">
                            <?php if (!empty($room['is_public'])): ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <i class="fas fa-globe text-xs mr-1"></i>Public
                                </span>
                            <?php endif; ?>
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                <?= ucfirst(htmlspecialchars($room['role'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="./room?key=<?= htmlspecialchars($room['room_key']) ?>"
                            class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold transition-all group-hover:gap-3">
                            Open Room <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-room-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const card = this.closest('.glass-card');
                const roomName = card ? card.querySelector('h3').innerText.trim() : 'this room';

                Swal.fire({
                    title: 'Delete Room?',
                    html: `Are you sure you want to delete <strong>"${roomName}"</strong>?<br><small class="text-gray-500">This action cannot be undone.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6366f1',
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>