<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <div class="space-x-4">
            <span class="text-gray-600 dark:text-gray-300">Welcome,
                <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="./logout" class="text-red-500 hover:text-red-700 text-sm font-medium">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Join Room -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white"><i
                    class="fas fa-sign-in-alt mr-2 text-primary-500"></i>Join Existing Room</h2>
            <form action="./join-room" method="POST">
                <div class="flex gap-2">
                    <input type="text" name="room_key" placeholder="Enter Room Key" required
                        class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:outline-none focus:shadow-outline focus:border-primary-500">
                    <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Join
                    </button>
                </div>
            </form>
        </div>

        <!-- Create Room -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white"><i
                    class="fas fa-plus-circle mr-2 text-green-500"></i>Create New Room</h2>
            <form action="./create-room" method="POST">
                <div class="flex gap-2">
                    <input type="text" name="name" placeholder="Room Name (e.g. Project X)" required
                        class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:outline-none focus:shadow-outline focus:border-primary-500">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- My Rooms -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Your Rooms</h2>
        <?php if (empty($myRooms) && empty($sharedRooms)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 dark:text-gray-400">
                You haven't created or joined any rooms yet.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($myRooms as $room): ?>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex justify-between items-center transition hover:shadow-md border-l-4 border-primary-500">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars($room['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500">Key: <span
                                    class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded"><?= htmlspecialchars($room['room_key']) ?></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Owner
                            </span>
                            <a href="./room?key=<?= htmlspecialchars($room['room_key']) ?>"
                                class="text-primary-600 hover:text-primary-800 font-medium">Open <i
                                    class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($sharedRooms as $room): ?>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex justify-between items-center transition hover:shadow-md border-l-4 border-gray-400">
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars($room['name']) ?>
                            </h3>
                            <p class="text-xs text-gray-500">Key: <span
                                    class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded"><?= htmlspecialchars($room['room_key']) ?></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= ucfirst(htmlspecialchars($room['role'])) ?>
                            </span>
                            <a href="./room?key=<?= htmlspecialchars($room['room_key']) ?>"
                                class="text-primary-600 hover:text-primary-800 font-medium">Open <i
                                    class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>