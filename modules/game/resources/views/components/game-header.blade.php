@props(['info'])
<?php

use Dnw\Game\Http\ViewModel\GameInformationViewModel;

/** @var GameInformationViewModel $info */
?>


<div class="content-bare content-board-header">
    <div class="boardHeader">
        <div>
            <div class="titleBar">
                <div class="titleBarRightSide">
                    <span class="gameTimeRemaining">
                        <span class="gameTimeRemainingNextPhase">{{ $info->nextText }}:</span>
                        <span class="timeremaining" data-unixtime="{{ $info->nextAsUnixTime }}"></span>
                        <span class="timestampGamesWrapper"> (<span
                                class="timestampGames">{{ $info->nextAsDateTime }}</span>) </span>
                    </span>
                    <div style="clear:both"></div>
                </div>
                <div class="titleBarLeftSide">
                    <span class="gameName">{{ $info->name }}</span>
                </div>
                <div style="clear:both"></div>
                <div class="titleBarRightSide">
                    <div><span class="gameHoursPerPhase"><strong>{{ $info->phaseLength }}</strong>
                            /{{ $info->phaseLabel }}</span>
                    </div>
                </div>
                <div class="titleBarLeftSide">
                    <div>
                        @if ($info->currentPhase)
                            <span class="gameDate">{{ $info->currentPhase }}</span>,
                        @endif
                        <span class="gamePhase">{{ $info->currentPhaseType }}</span>
                    </div>
                    <div>
                        <div class="titleBarLeftSide">
                            <span class="gamePotType"><a class="light"
                                    href="{{ $info->variantLink }}">
                                    {{ $info->variant }}</a>@if ($info->additionalInformation),
                                {{ $info->additionalInformation }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div style="clear:both"></div>
                {{-- <div class="titleBarRightSide"></div> --}}
                <div style="clear:both"></div>
            </div>
            {{-- <div class="panelBarGraphTop occupationBar"> --}}
            {{--     <table class="occupationBarTable"> --}}
            {{--         <tbody> --}}
            {{--             <tr> --}}
            {{--                 <td class="occupationBarJoined first" style="width:12%"></td> --}}
            {{--                 <td class="occupationBarNotJoined" style="width:88%"></td> --}}
            {{--             </tr> --}}
            {{--         </tbody> --}}
            {{--     </table> --}}
            {{-- </div> --}}
        </div>
    </div>
</div>
