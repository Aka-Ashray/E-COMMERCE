<?php
include 'db_config.php';

$cart_count = 0;
if(isset($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_count = $result->fetch_assoc()['count'];
}
?>
<nav class="bg-gray-900 p-2">
    <div class="container mx-auto">
        <!-- Top Navigation -->
        <div class="flex items-center justify-between py-2">
            <a href="index.php" class="text-white text-2xl font-bold">Cupboard</a>
            
            <!-- Search Bar -->
            <div class="flex-1 mx-8">
                <form action="search.php" method="GET" class="flex">
                    <select name="category" class="px-4 py-2 rounded-l">
                        <option value="all">All Categories</option>
                        <option value="cpu">CPU</option>
                        <option value="gpu">GPU</option>
                        <option value="ram">RAM</option>
                        <option value="storage">Storage</option>
                    </select>
                    <input type="text" name="q" placeholder="Search products..." 
                           class="flex-1 p-2 border-none focus:outline-none">
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 px-6 py-2 rounded-r">
                        Search
                    </button>
                </form>
            </div>
            
            <!-- Right Menu -->
            <div class="flex items-center space-x-6">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="admin.php" class="text-yellow-400 hover:text-yellow-500 font-bold">Admin Panel</a>
                    <?php endif; ?>
                    <div class="text-white">
                        <div class="text-xs">Hello, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></div>
                    </div>
                    <a href="cart.php" class="text-white flex items-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="ml-1 font-bold">Cart</span>
                        <?php if($cart_count > 0): ?>
                            <span class="ml-1 bg-yellow-400 text-black rounded-full px-2 cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="logout.php" class="text-white hover:text-yellow-400">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-white">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Categories Navigation -->
        <div class="flex space-x-6 text-white py-2 text-sm">
            <a href="category.php?type=cpu" class="hover:text-yellow-400">Processors</a>
            <a href="category.php?type=gpu" class="hover:text-yellow-400">Graphics Cards</a>
            <a href="category.php?type=ram" class="hover:text-yellow-400">Memory</a>
            <a href="category.php?type=storage" class="hover:text-yellow-400">Storage</a>
            <a href="category.php?type=motherboard" class="hover:text-yellow-400">Motherboards</a>
        </div>
    </div>
</nav>
