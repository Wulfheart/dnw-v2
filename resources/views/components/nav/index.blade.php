<?php
    /** @var \App\ViewModel\User\UserInfoViewModel $userInfo */
    ?>

<div id="header">
    <div id="header-container">
        <a href="./">
            <img id="logo" src="images/logo.png" alt="webDiplomacy">
        </a>
        <div>
            <div id="header-welcome">
                Welcome,
                @if($userInfo->isAuthenticated)
                    <a href="./userprofile.php?userID=84364" profilelinkuserid="84364">{{ $userInfo->name->unwrap() }}</a>
                    -
                    <span class="logon">(<a href="logon.php?logoff=on" class="light">Log off</a>)</span>
                @else
                    Guest
                    -
                    <span class="logon">(<a href="logon.php?logoff=on" class="light">Login</a>)</span>
                @endif

            </div>
            <div id="header-goto">
                <div class="nav-wrap">
                    <div class="nav-tab"><a href="index.php?" title="See what's happening">Home</a></div>
                    <div class="nav-tab"><a href="/contrib/phpBB3/"
                                            title="The forum; chat, get help, help others, arrange games, discuss strategies">Forum</a>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Search ▼
                        <div id="nav-drop">
                            <a href="dev-login">Find User</a>
                            <a href="gamelistings.php?gamelistType=Search">Game Search</a>
                            <a href="detailedSearch.php" title="advanced search of users and games">Advanced Search</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Games ▼
                        <div id="nav-drop">
                            <a href="gamelistings.php?gamelistType=New"
                               title="Game listings; a searchable list of the games on this server">New Games</a>
                            <a href="gamelistings.php?gamelistType=Open%20Positions"
                               title="Open positions dropped by other players, free to claim">Open Games</a>
                            <a href="gamecreate.php" title="Start up a new game">Start a New Game</a>
                            <a href="botgamecreate.php" title="Start up a new bots-only game">Start an AI/Bot Game</a>
                            <a href="gamecreateSandbox.php" title="Start up a new bots-only game">Start a Sandbox
                                Game</a>
                            <a href="gamelistings.php?gamelistType=Active"
                               title="View/Spectate games currently running">Active Games</a>
                            <!-- <a href="ghostRatings.php" title="Ghost Ratings Information">Ghost Ratings</a> -->
                            <a href="tournaments.php"
                               title="Information about tournaments on webDiplomacy">Tournaments</a>
                            <a href="halloffame.php" title="Information about tournaments on webDiplomacy">Hall of
                                Fame</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Account ▼
                        <div id="nav-drop">
                            <a href="contrib/phpBB3/ucp.php?i=pm" title="Read your messages">Private Messages</a>
                            <a href="contrib/phpBB3/ucp.php?i=179" title="Change your forum user settings">Forum
                                Settings</a>
                            <a href="usercp.php" title="Change your user specific settings">Settings</a>
                            <a href="group.php" title="Manage your user relationships">Relationships</a>
                            <a href="useridentity.php" title="Verify your identity">Identity</a>
                            <a href="usernotifications.php" title="Manage site notifications">Notifications</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Help ▼
                        <div id="nav-drop">
                            <a href="rules.php">Site Rules</a>
                            <a href="faq.php" title="Frequently Asked Questions">FAQ</a>
                            <a href="intro.php" title="Intro to Diplomacy">Diplomacy Intro</a>
                            <a href="points.php" title="Points and Scoring Systems">Points/Scoring</a>
                            <a href="variants.php" title="Active webDiplomacy variants">Variants</a>
                            <a href="help.php" title="Site information; guides, stats, links">More Info</a>
                            <a href="modforum.php">Get Help</a>
                            <a href="donations.php">Donate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="seperator"></div>
