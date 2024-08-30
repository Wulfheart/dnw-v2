// Above are timestamp functions, below are countdown functions:

var timerCheck = false; // Stores the PeriodicalExecutor which updates countdowns
var timerCheckMinTime = 7 * 24 * 60 * 60; // The refresh time for the PeriodicalExecutor
var newTimerCheckMinTime = 7 * 24 * 60 * 60; // The new refresh time, to detect when it has to be restarted at a new rate

class PeriodicalExecuter {
    constructor(callback, frequency) {
        this.callback = callback;
        this.frequency = frequency;
        this.currentlyExecuting = false;

        this.registerCallback();
    }

    registerCallback() {
        this.timer = setInterval(this.onTimerEvent.bind(this), this.frequency * 1000);
    }

    execute() {
        this.callback(this);
    }

    stop() {
        if (!this.timer) return;
        clearInterval(this.timer);
        this.timer = null;
    }

    onTimerEvent() {
        if (!this.currentlyExecuting) {
            try {
                this.currentlyExecuting = true;
                this.execute();
                this.currentlyExecuting = false;
            } catch (e) {
                this.currentlyExecuting = false;
                throw e;
            }
        }
    }
}


// Update countdown timers, needs to be run repeatedly. The first time it is run it will set up future runs
export function updateTimers(timerConfig) {
    const timeFrom = Math.floor((new Date).getTime() / 1000);

    const elems = document.getElementsByClassName("timeremaining")
    for(const c of elems) {

        const givenTime = parseInt(c.getAttribute("data-unixtime"));
        if(isNaN(givenTime)) {
            console.error("Invalid time given to timeremaining element", c);
        }
        const secondsRemaining = givenTime - timeFrom;

        if (secondsRemaining < 300) {
            c.classList.add('timeremaining-urgent');
        }
        if (secondsRemaining < 0) {
            c.classList.remove('timeremaining-urgent');
            c.classList.add('timeremaining-expired');
        }

        c.innerHTML = remainingText(secondsRemaining, timerConfig["units"]);

        setMinimumTimerInterval(1)

    }

    // If the timer interval has changed update it
    if (newTimerCheckMinTime != timerCheckMinTime) {
        timerCheckMinTime = newTimerCheckMinTime;

        if (typeof timerCheck == "object")
            timerCheck.stop();

        timerCheck = new PeriodicalExecuter(() => updateTimers(timerConfig), timerCheckMinTime);
    }
}

// Update the timer update period, if 1 the countdowns are updated every second. The smallest update period has to be used
function setMinimumTimerInterval(newInterval) {
    if (newInterval < 1.0) newInterval = 1;

    if (newTimerCheckMinTime >= newInterval)
        newTimerCheckMinTime = newInterval;
}


// Textual time remaining for a given number of seconds to pass. Also sets the minimum timer interval
function remainingText(secondsRemaining, unitStrings) {
    if (secondsRemaining <= 0) {
        return unitStrings["now"];
    }

    let seconds = Math.floor(secondsRemaining % 60);
    let minutes = Math.floor((secondsRemaining % (60 * 60)) / 60);
    let hours = Math.floor(secondsRemaining % (24 * 60 * 60) / (60 * 60));
    let days = Math.floor(secondsRemaining / (24 * 60 * 60));

    // const daysComponent = days > 0 ? Intl.NumberFormat(locale).format(days) : '';
    // const hoursComponent = hours > 0 ? Intl.NumberFormat(locale, {minimumIntegerDigits: 2}).format(hours) : '';
    // const minutesComponent = Intl.NumberFormat(locale, {minimumIntegerDigits: 2}).format(minutes);
    // const secondsComponent = Intl.NumberFormat(locale, {minimumIntegerDigits: 2}).format(seconds);
    // setMinimumTimerInterval(1);
    //
    // return `${daysComponent}:${hoursComponent}:${minutesComponent}:${secondsComponent}`;

    const daysString = unitStrings["days"];
    const daysStringSingular = unitStrings["day"];
    const hoursString = unitStrings["hours"];
    const hoursStringSingular = unitStrings["hour"];
    const minutesString = unitStrings["minutes"];
    const minutesStringSingular = unitStrings["minute"];
    const secondsString = unitStrings["seconds"];
    const secondsStringSingular = unitStrings["second"];


    const daysFormatted = days != 1 ? `${days} ${daysString}` : `${days} ${daysStringSingular}`;
    const hoursFormatted = hours != 1 ? `${hours} ${hoursString}` : `${hours} ${hoursStringSingular}`;
    const minutesFormatted = minutes != 1 ? `${minutes} ${minutesString}` : `${minutes} ${minutesStringSingular}`;
    const secondsFormatted = seconds != 1 ? `${seconds} ${secondsString}` : `${seconds} ${secondsStringSingular}`;

    if (days > 0) // D, H
    {
        minutes += Math.round(seconds / 60); // Add a minute if the seconds almost give a minute
        hours += Math.round(minutes / 60); // Add an hour if the minutes almost gives an hour

        if (days < 2) {
            return `${daysFormatted} ${hoursFormatted}`;
        } else {
            return `${daysFormatted}`;
        }
    } else if (hours > 0) // H, M
    {
        minutes += Math.round(seconds / 60); // Add a minute if the seconds almost give a minute)

        if (hours < 4) {
            return `${hoursFormatted} ${minutesFormatted}`;
        } else {

            hours += Math.round(minutes / 60); // Add an hour if the minutes almost gives an hour

            return `${hoursFormatted}`;
        }
    } else // M, S
    {
        if (minutes >= 5) {
            return `${minutesFormatted}`;
        } else {

            if (minutes > 0)
                return `${minutesFormatted} ${secondsFormatted}`;
            else
                return `${secondsFormatted}`;
        }
    }
}
