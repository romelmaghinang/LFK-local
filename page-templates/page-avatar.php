<?php 
/* Template Name: Avatar Page Template */ 

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); // display header
?>

<div class='main-mid'>
<div class='_maxwrap'>
<div class="avatar-wrap">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Loading Dress your Avatar</div>
    </div>

    <div id="messageContainer" class="message-container">
        <h2 class="modal-title" id="messageTitle"></h2>
        <p id="messageText"></p>
    </div>
    
    <div id="mainContainer" class="main-container">
        <div class="avatar-section">
            <div class="avatar-header">
	           	<button onclick="goBackOrHome()" class="avatar-back-button">
	                Back
	            </button>
            </div>
            <h1 class="avatar-title">Dress your Avatar</h1>
     
            <div class="avatar-display">
                <div class="avatar-character" id="avatarCharacter"></div>
            </div>
           	<div class="rewards-stats-wrapper">
                <div class="stat-block">
                    <span class="stat-label" style="display:block; text-align:center !important;">
                        Your Total Feathers
                    </span>
                    <div class="credits-display">
                        <span id="userCredits">0</span>
                        <img src="https://lote4kids.com/wp-content/uploads/2025/07/IMG_7049.png" alt="Feather Icon">
                    </div>
                </div>
			</div>
        	<div class="category-tabs">
                <button class="category-tab" data-category="headwear">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_hats.png" alt="Headwear Icon">
                    Headwear
                </button>
                <button class="category-tab active" data-category="tops">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_tops.png" alt="Tops Icon">
                    Tops
                </button>
                <button class="category-tab" data-category="shoes">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_shoes.png" alt="Shoes Icon">
                    Shoes
                </button>
                <button class="category-tab" data-category="facewear">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_glasses.png" alt="Facewear Icon">
                    Facewear
                </button>
                <button class="category-tab" data-category="bottoms">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_bottoms.png" alt="Bottoms Icon">
                    Bottoms
                </button>
                <button class="category-tab" data-category="unlocked">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/lekti_unlocked.png" alt="Unlocked Items Icon">
                    Unlocked Items
                </button>
            </div>
            <div class="unlocked-items">
                <div class="unlocked-title">
                    Active Items
                    <span id="unlockedCount">0</span>
                </div>
                <div class="unlocked-grid" id="unlockedItemsGrid">
                    <div class="empty-unlocked-text" style="display: none;" id="emptyUnlockedText">
                        You haven't unlocked any items yet. Purchase items to customize your avatar!
                    </div>
                </div>
            </div>
			<button id="saveAvatarBtn" class="save-avatar-btn">Save Avatar</button>
    	</div>
        <div class="items-section">
            <div class="category-header">
                <h2 class="category-title" id="categoryTitle">Select Headwear</h2>
            </div>
            <div id="items-container">
                <div class="loading">
                    <div style="text-align: center; padding: 20px;">
                        <div class="spinner"></div>
                        <p style="margin-top: 10px; font-size: 14px; color: #555;">
                            Loading items... please wait.
                        </p>
                    </div>
                </div>
            </div>
        </div>
	</div>

</div>
</div>
</div>

<?php get_footer(); ?> 