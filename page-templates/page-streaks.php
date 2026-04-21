<?php /* Template Name: Streaks Page Template */ 

l4k_checkMemberLoggedIn(); // check if member is logged in
get_header(); // display header
?>

<div class='main-mid'>
	<div class='_maxwrap'>
  

    <span class="bookshelf-heading">📚 Streaks Bookshelf 📚</span>
    <div class="bookshelf-container">
        <button onclick="goBackOrHome()" class="back-button">
            Back
        </button>

        <!-- App Usage Streak -->
        <div class="shelf">
            <div class="books">
                <div class="category-label">
                    <span class="dot"></span>
                    App Usage Streak
                    <span class="dot"></span>
                </div>
                <div class="book blue-book" data-book="Hatchling" data-category="App Usage Streak" data-color="blue" data-required="1" data-description="Just hatched and beginning your streak journey.">
                    <span class="book-title">Hatchling</span>
                </div>
                <div class="book blue-book disabled" data-book="Owlet" data-category="App Usage Streak" data-color="blue" data-required="3" data-description="A little owl learning new things every day."><span class="book-title">Owlet</span></div>
                <div class="book blue-book disabled" data-book="Flapper" data-category="App Usage Streak" data-color="blue" data-required="5" data-description="Starting to flap your wings and explore your growing streak."><span class="book-title">Flapper</span></div>
                <div class="book blue-book disabled" data-book="Soarer" data-category="App Usage Streak" data-color="blue" data-required="7" data-description="Flying higher and getting stronger with each new streak."><span class="book-title">Soarer</span></div>
                <div class="book blue-book disabled" data-book="Sky Flyer" data-category="App Usage Streak" data-color="blue" data-required="14" data-description="Gliding towards learning and streak mastery."><span class="book-title">Sky Flyer</span></div>
                <div class="book blue-book disabled" data-book="Hunter" data-category="App Usage Streak" data-color="blue" data-required="21" data-description="Growing sharper and braver — focused like an owl on the hunt."><span class="book-title">Hunter</span></div>
                <div class="book blue-book disabled" data-book="Hootmaster" data-category="App Usage Streak" data-color="blue" data-required="30" data-description="A clever and confident streak leader."><span class="book-title">Hootmaster</span></div>
                <div class="book blue-book disabled" data-book="Sky Hero" data-category="App Usage Streak" data-color="blue" data-required="45" data-description="Flying fast and far — a true hero of the streak."><span class="book-title">Sky Hero</span></div>
                <div class="book blue-book disabled" data-book="Wise Owl" data-category="App Usage Streak" data-color="blue" data-required="60" data-description="An experienced learner."><span class="book-title">Wise Owl</span></div>
                <div class="book blue-book disabled" data-book="Starwing" data-category="App Usage Streak" data-color="blue" data-required="100" data-description="A legendary streak owl whose wings reach the stars."><span class="book-title">Starwing</span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="150"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="200"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="250"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="300"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="350"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="400"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="450"><span class="book-title"></span></div>
                <div class="book blue-book disabled" data-book="" data-category="App Usage Streak" data-color="blue" data-required="500"><span class="book-title"></span></div>
            </div>
            <div class="shelf-wood"></div>
        </div>

        <!-- Books Streak -->
        <div class="shelf">
            <div class="books">
                <div class="category-label">
                    <span class="dot"></span>
                    Books Streak
                    <span class="dot"></span>
                </div>
                <div class="book green-book" data-book="Hatchling" data-category="Books Streak" data-color="green" data-required="1" data-description="Just hatched and beginning your streak journey."><span class="book-title">Hatchling</span></div>
                <div class="book green-book disabled" data-book="Owlet" data-category="Books Streak" data-color="green" data-required="3" data-description="A little owl learning new things every day."><span class="book-title">Owlet</span></div>
                <div class="book green-book disabled" data-book="Flapper" data-category="Books Streak" data-color="green" data-required="5" data-description="Starting to flap your wings and explore your growing streak."><span class="book-title">Flapper</span></div>
                <div class="book green-book disabled" data-book="Soarer" data-category="Books Streak" data-color="green" data-required="10" data-description="Flying higher and getting stronger with each new streak."><span class="book-title">Soarer</span></div>
                <div class="book green-book disabled" data-book="Sky Flyer" data-category="Books Streak" data-color="green" data-required="20" data-description="Gliding towards learning and streak mastery."><span class="book-title">Sky Flyer</span></div>
                <div class="book green-book disabled" data-book="Hunter" data-category="Books Streak" data-color="green" data-required="30" data-description="Growing sharper and braver — focused like an owl on the hunt."><span class="book-title">Hunter</span></div>
                <div class="book green-book disabled" data-book="Hootmaster" data-category="Books Streak" data-color="green" data-required="50" data-description="A clever and confident streak leader."><span class="book-title">Hootmaster</span></div>
                <div class="book green-book disabled" data-book="Sky Hero" data-category="Books Streak" data-color="green" data-required="75" data-description="Flying fast and far — a true hero of the streak."><span class="book-title">Sky Hero</span></div>
                <div class="book green-book disabled" data-book="Wise Owl" data-category="Books Streak" data-color="green" data-required="100" data-description="An experienced learner."><span class="book-title">Wise Owl</span></div>
                <div class="book green-book disabled" data-book="Starwing" data-category="Books Streak" data-color="green" data-required="150" data-description="A legendary streak owl whose wings reach the stars."><span class="book-title">Starwing</span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="200"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="250"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="300"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="350"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="400"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="450"><span class="book-title"></span></div>
                <div class="book green-book disabled" data-book="" data-category="Books Streak" data-color="green" data-required="500"><span class="book-title"></span></div>
            </div>
            <div class="shelf-wood"></div>
        </div>

        <!-- Activities Streak -->
        <div class="shelf">
            <div class="books">
                <div class="category-label">
                    <span class="dot"></span>
                    Activities Streak
                    <span class="dot"></span>
                </div>
                <div class="book purple-book" data-book="Hatchling" data-category="Activities Streak" data-color="purple" data-required="1" data-description="Just hatched and beginning your streak journey."><span class="book-title">Hatchling</span></div>
                <div class="book purple-book disabled" data-book="Owlet" data-category="Activities Streak" data-color="purple" data-required="3" data-description="A little owl learning new things every day."><span class="book-title">Owlet</span></div>
                <div class="book purple-book disabled" data-book="Flapper" data-category="Activities Streak" data-color="purple" data-required="5" data-description="Starting to flap your wings and explore your growing streak."><span class="book-title">Flapper</span></div>
                <div class="book purple-book disabled" data-book="Soarer" data-category="Activities Streak" data-color="purple" data-required="10" data-description="Flying higher and getting stronger with each new streak."><span class="book-title">Soarer</span></div>
                <div class="book purple-book disabled" data-book="Sky Flyer" data-category="Activities Streak" data-color="purple" data-required="15" data-description="Gliding towards learning and streak mastery."><span class="book-title">Sky Flyer</span></div>
                <div class="book purple-book disabled" data-book="Hunter" data-category="Activities Streak" data-color="purple" data-required="20" data-description="Growing sharper and braver — focused like an owl on the hunt."><span class="book-title">Hunter</span></div>
                <div class="book purple-book disabled" data-book="Hootmaster" data-category="Activities Streak" data-color="purple" data-required="30" data-description="A clever and confident streak leader."><span class="book-title">Hootmaster</span></div>
                <div class="book purple-book disabled" data-book="Sky Hero" data-category="Activities Streak" data-color="purple" data-required="40" data-description="Flying fast and far — a true hero of the streak."><span class="book-title">Sky Hero</span></div>
                <div class="book purple-book disabled" data-book="Wise Owl" data-category="Activities Streak" data-color="purple" data-required="50" data-description="An experienced learner."><span class="book-title">Wise Owl</span></div>
                <div class="book purple-book disabled" data-book="Starwing" data-category="Activities Streak" data-color="purple" data-required="75" data-description="A legendary streak owl whose wings reach the stars."><span class="book-title">Starwing</span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="100"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="150"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="200"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="250"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="300"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="350"><span class="book-title"></span></div>
                <div class="book purple-book disabled" data-book="" data-category="Activities Streak" data-color="purple" data-required="400"><span class="book-title"></span></div>
            </div>
            <div class="shelf-wood"></div>
        </div>

        <!-- Languages Explored -->
        <div class="shelf">
            <div class="books">
                <div class="category-label">
                    <span class="dot"></span>
                    Languages Explored
                    <span class="dot"></span>
                </div>
                <div class="book gold-book" data-book="Hatchling" data-category="Languages Explored" data-color="gold" data-required="1" data-description="Just hatched and beginning your language journey."><span class="book-title">Hatchling</span></div>
                <div class="book gold-book disabled" data-book="Owlet" data-category="Languages Explored" data-color="gold" data-required="3" data-description="A little owl learning new languages every day."><span class="book-title">Owlet</span></div>
                <div class="book gold-book disabled" data-book="Flapper" data-category="Languages Explored" data-color="gold" data-required="5" data-description="Starting to flap your wings and explore your growing language skills."><span class="book-title">Flapper</span></div>
                <div class="book gold-book disabled" data-book="Soarer" data-category="Languages Explored" data-color="gold" data-required="10" data-description="Flying higher and getting stronger with each new language."><span class="book-title">Soarer</span></div>
                <div class="book gold-book disabled" data-book="Sky Flyer" data-category="Languages Explored" data-color="gold" data-required="15" data-description="Gliding towards language mastery."><span class="book-title">Sky Flyer</span></div>
                <div class="book gold-book disabled" data-book="Hunter" data-category="Languages Explored" data-color="gold" data-required="20" data-description="Growing sharper and braver — focused like an owl on the hunt for languages."><span class="book-title">Hunter</span></div>
                <div class="book gold-book disabled" data-book="Hootmaster" data-category="Languages Explored" data-color="gold" data-required="30" data-description="A clever and confident language leader."><span class="book-title">Hootmaster</span></div>
                <div class="book gold-book disabled" data-book="Sky Hero" data-category="Languages Explored" data-color="gold" data-required="40" data-description="Flying fast and far — a true hero of languages."><span class="book-title">Sky Hero</span></div>
                <div class="book gold-book disabled" data-book="Wise Owl" data-category="Languages Explored" data-color="gold" data-required="50" data-description="An experienced polyglot learner."><span class="book-title">Wise Owl</span></div>
                <div class="book gold-book disabled" data-book="Starwing" data-category="Languages Explored" data-color="gold" data-required="75" data-description="A legendary language owl whose wings reach the stars."><span class="book-title">Starwing</span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="100"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="150"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="200"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="250"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="300"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="350"><span class="book-title"></span></div>
                <div class="book gold-book disabled" data-book="" data-category="Languages Explored" data-color="gold" data-required="400"><span class="book-title"></span></div>
            </div>
            <div class="shelf-wood"></div>
        </div>
    </div>

    <!-- Modal -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <div id="modalTitle">Activities Streak</div>
            </div>
            <div class="modal-book-section" id="modalBookSection">
                <div class="modal-book" id="modalBook" onclick="openBook()">
                    <div class="book-title" id="modalBookTitle">Hatchling</div>
                    <div class="tap-to-open" id="tapToOpen">Tap to open</div>
                </div>
                <div class="book-opening-overlay" id="bookOpeningOverlay">
                    <div class="book-3d-container" id="book3DContainer">
                        <div class="book-open" id="bookOpen">
                            <div class="book-page-left" id="bookPageLeft">
                                <div class="achievement-text">You have earned using the app 1 day</div>
                            </div>
                            <div class="book-page-right" id="bookPageRight">
                                <div class="congratulations">Great job!</div>
                            </div>
                            <div class="book-binding"></div>
                        </div>
                        <div class="book-pages-container" id="bookPagesContainer">
                            <div class="book-page">
                                <div class="page-content"></div>
                            </div>
                            <div class="book-page">
                                <div class="page-content"></div>
                            </div>
                            <div class="book-page">
                                <div class="page-content"></div>
                            </div>
                            <div class="book-page">
                                <div class="page-content"></div>
                            </div>
                            <div class="book-page">
                                <div class="page-content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-description" id="modalDescription">
                Just hatched and beginning your streak journey.
            </div>
            <button class="close-btn" id="closeBtn" onclick="closeModal()">Put it back on the shelf</button>
            <button class="close-btn" id="closeBookBtn" onclick="closeBook()" style="display: none;">Close</button>
        </div>
    </div>

    <!-- Stats Display -->
    <!-- <div class="stat-blocks-container">
        <div class="stat-block">
            <span class="stat-label">App Usage streaks</span>
            <a href="/bookshelf" class="credits-display" style="text-decoration: none; color: inherit; cursor: pointer;">
                <span id="daysCount">0</span>
                <span style="font-size: 24px;">📅</span>
            </a>
        </div>
        <div class="stat-block">
            <span class="stat-label">Books streak</span>
            <a href="/bookshelf" class="credits-display" style="text-decoration: none; color: inherit; cursor: pointer;">
                <span id="bookCount">0</span>
                <span style="font-size: 24px;">📖</span>
            </a>
        </div>
        <div class="stat-block">
            <span class="stat-label">Activities streak</span>
            <a href="/bookshelf" class="credits-display" style="text-decoration: none; color: inherit; cursor: pointer;">
                <span id="activityCount">0</span>
                <span style="font-size: 24px;">📚</span>
            </a>
        </div>

        <div class="stat-block">
            <span class="stat-label">Languages streak</span>
            <a href="/bookshelf" class="credits-display" style="text-decoration: none; color: inherit; cursor: pointer;">
                <span id="languageCount">0</span>
                <span style="font-size: 24px;">🌍</span>
            </a>
        </div>
    </div> -->
	</div>
</div>

<?php get_footer(); ?>