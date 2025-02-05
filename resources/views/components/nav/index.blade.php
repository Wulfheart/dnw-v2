<?php

use App\ViewModel\User\UserInfoViewModel;

/** @var UserInfoViewModel $userInfo */
?>

<div id="header">
    <div id="header-container">
        <a href="/">
            <img id="logo" src="images/logo.png" alt="webDiplomacy">
        </a>
        <div>
            <div id="header-welcome">
                Welcome,
                @if ($userInfo->isAuthenticated)
                    <a href="./userprofile.php?userID=84364">{{ $userInfo->name->unwrap() }}</a>
                    -
                    <span class="logon">(<a href="#" hx-delete="{{ route('logout') }}" class="light">Log
                            off</a>)</span>
                @else
                    Guest
                    -
                    <span class="logon"><a href="{{ route('login') }}" class="light">Login</a>)</span>
                @endif

            </div>
            <div id="header-goto">
                <div class="nav-wrap">
                    <div class="nav-tab"><a href="/" title="See what's happening">Home</a></div>
                    <div class="nav-tab"><a href="/"
                            title="The forum; chat, get help, help others, arrange games, discuss strategies">Forum</a>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Search ▼
                        <div id="nav-drop">
                            <a href="#">Find User</a>
                            <a href="#">Game Search</a>
                            <a href="#" title="advanced search of users and games">Advanced Search</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Games ▼
                        <div id="nav-drop">
                            <a href="#" title="Game listings; a searchable list of the games on this server">New
                                Games</a>
                            <a href="#" title="Open positions dropped by other players, free to claim">Open
                                Games</a>
                            <a href="{{ route('game.create') }}" title="Start up a new game">Start a New Game</a>
                            <a href="#" title="View/Spectate games currently running">Active Games</a>
                            <!-- <a href="ghostRatings.php" title="Ghost Ratings Information">Ghost Ratings</a> -->
                            <a href="#" title="Information about tournaments on webDiplomacy">Hall of
                                Fame</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Account ▼
                        <div id="nav-drop">
                            <a href="#" title="Read your messages">Private Messages</a>
                            <a href="#" title="Change your forum user settings">Forum
                                Settings</a>
                            <a href="#" title="Change your user specific settings">Settings</a>
                            <a href="#" title="Manage your user relationships">Relationships</a>
                            <a href="#" title="Verify your identity">Identity</a>
                            <a href="#" title="Manage site notifications">Notifications</a>
                        </div>
                    </div>
                    <div id="navSubMenu" class="clickable nav-tab">Help ▼
                        <div id="nav-drop">
                            <a href="#">Site Rules</a>
                            <a href="#" title="Frequently Asked Questions">FAQ</a>
                            <a href="#" title="Intro to Diplomacy">Diplomacy Intro</a>
                            <a href="#" title="Points and Scoring Systems">Points/Scoring</a>
                            <a href="#" title="Active webDiplomacy variants">Variants</a>
                            <a href="#" title="Site information; guides, stats, links">More Info</a>
                            <a href="#">Get Help</a>
                            <a href="#">Donate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="seperator"></div>
