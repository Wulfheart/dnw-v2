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
export function updateTimers() {
    const timeFrom = Math.floor((new Date).getTime() / 1000);

    const elems = document.getElementsByClassName("timeremaining")
    for(const c of elems) {

        const givenTime = parseInt(c.getAttribute("data-unixtime"));
        const secondsRemaining = givenTime - timeFrom;

        if (secondsRemaining < 300) {
            c.classList.add('timeremaining-urgent');
        }
        if (secondsRemaining < 0) {
            c.classList.remove('timeremaining-urgent');
            c.classList.add('timeremaining-expired');
        }

        c.innerHTML = remainingText(secondsRemaining);

    }

    // If the timer interval has changed update it
    if (newTimerCheckMinTime != timerCheckMinTime) {
        timerCheckMinTime = newTimerCheckMinTime;

        if (typeof timerCheck == "object")
            timerCheck.stop();

        timerCheck = new PeriodicalExecuter(updateTimers, timerCheckMinTime);
    }
}

// Update the timer update period, if 1 the countdowns are updated every second. The smallest update period has to be used
function setMinimumTimerInterval(newInterval) {
    if (newInterval < 1.0) newInterval = 1;

    if (newTimerCheckMinTime >= newInterval)
        newTimerCheckMinTime = newInterval;
}


// Textual time remaining for a given number of seconds to pass. Also sets the minimum timer interval
function remainingText(secondsRemaining) {
    if (secondsRemaining <= 0) {
        return 'Now';
    }

    var seconds = Math.floor(secondsRemaining % 60);
    var minutes = Math.floor((secondsRemaining % (60 * 60)) / 60);
    var hours = Math.floor(secondsRemaining % (24 * 60 * 60) / (60 * 60));
    var days = Math.floor(secondsRemaining / (24 * 60 * 60));

    if (days > 0) // D, H
    {
        minutes += Math.round(seconds / 60); // Add a minute if the seconds almost give a minute
        hours += Math.round(minutes / 60); // Add an hour if the minutes almost gives an hour

        if (days < 2) {
            setMinimumTimerInterval(60 * minutes);
            return `${days} days, ${hours} hours`;
        } else {
            setMinimumTimerInterval(60 * 60 * hours);
            return `${days} days`;
        }
    } else if (hours > 0) // H, M
    {
        minutes += Math.round(seconds / 60); // Add a minute if the seconds almost give a minute)

        if (hours < 4) {
            setMinimumTimerInterval(seconds);
            return `${hours} hours, ${minutes} mins`;
        } else {
            setMinimumTimerInterval(minutes * 60);

            hours += Math.round(minutes / 60); // Add an hour if the minutes almost gives an hour

            return `${hours} hours`;
        }
    } else // M, S
    {
        if (minutes >= 5) {
            setMinimumTimerInterval(seconds);
            return `${minutes} mins`;
        } else {
            setMinimumTimerInterval(1);

            if (minutes > 1)
                return `${minutes} mins, ${seconds} secs`;
            else if (minutes > 0)
                return `${minutes} min, ${seconds} secs`;
            else
                return `${seconds} secs`;
        }
    }
}
