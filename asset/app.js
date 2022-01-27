var filter = {
    topic: '',
    emote: '',
},
filterOperator = {
    topic: '=',
    emote: '*=',
},
currentStatus = 'resume',
queuedMessageCount = 0,
actions = {
    pushStatus: function(status) {
        setStatus(status);
        if(status === 'resume') {
            actions.resume();
        }
    },
    resume: function() {
        document.querySelectorAll('.message--while-pause').forEach(function(el) {
            el.classList.remove('message--while-pause');
        })
        this.resetQueue();
    },
    resetQueue: function() {
        queuedMessageCount = 0;
        document.getElementById('queue-count').textContent = '';
    },
    clear: function() {
        document.querySelector('main').innerHTML = '';
        filter = {
            topic: '',
            emote: '',
        };
        this.resetQueue();
    }
},
buttonActions = function(action) {
    if(action === currentStatus || document.body.classList.contains('body--splash')) {
        return;
    }
    if(action === 'clear') {
        actions.clear();
    } else {
        actions.pushStatus(action);
    }
    pushMessage({
        message: '<b>' + action.toUpperCase() + '</b> ' + document.title,
    }, true);
},
keysToAction = {
    KeyS: 'stop',
    KeyR: 'resume',
    KeyP: 'pause',
    KeyC: 'clear', 
},
setStatus = function(status) {
    console.log('wea', status)
    document.querySelectorAll('.button--active').forEach(function(el) {
        el.classList.remove("button--active")
    });
    document.querySelectorAll('[data-action="'+status+'"]')
        .forEach(function(el) {
            el.classList.add('button--active');
        });
    if(status === 'clear') {
        setStatus(currentStatus);
        return;
    }
    currentStatus = status;
    
    return currentStatus;
},
template = document.querySelector('#message');
pushMessage = function(data, isStatus = false) {
    let el = template.content.cloneNode(true);
    el.querySelector('.time').textContent = (new Date()).toTimeString().split(' ')[0];
    el.querySelector('.topic').textContent = data.topic;
    el.querySelector('.emote').textContent = data.emote;
    el.querySelector('.body-raw').innerHTML = data.message;
    var bodyFileDisplay = el.querySelector('.body-file-display');
    bodyFileDisplay.textContent = data.file_display_short;
    bodyFileDisplay.setAttribute('title', data.file_display);
    if(document.body.classList.contains('body--splash')) {
        document.body.classList.remove('body--splash', 'body--splash-in');
    }
    document.querySelector('main').prepend(el);
    el = document.querySelector('.message:first-child');
    el.dataset.emote = data.emote ? data.emote : '';
    el.dataset.topic = data.topic ? data.topic : '';
    if(!isStatus && currentStatus === 'pause') {
        queuedMessageCount++;
        el.classList.add('message--while-pause');
        document.getElementById('queue-count').textContent = queuedMessageCount;
    }
};
setStatus(currentStatus);
for(key in keysToAction) {
    document.querySelectorAll('[data-action="'+keysToAction[key]+'"]')
        .forEach(function(el) {
            el.addEventListener('click', function() {
                buttonActions(this.dataset.action);
            });
        })
}
document.addEventListener('keyup', function(event) {
    if(event.target.classList.contains("no-keys")) {
        return;
    }
    if(event.code in keysToAction) {
        buttonActions(keysToAction[event.code]);
    }
});
es = new EventSource('dump');
es.addEventListener('message', function (event) {
    if(currentStatus === 'stop') {
        return;
    }
    pushMessage(JSON.parse(event.data));
});
document.querySelector('.header-title')
    .addEventListener('paste', event => {
        event.preventDefault();
        var text = (event.originalEvent || event)
            .clipboardData.getData('text/plain');
        document.execCommand("insertHTML", false, text);
    });
document.querySelector('.header-title')  
    .addEventListener('input', event => {
        document.title = event.target.textContent;
    });
document.addEventListener('click', event => {
    var el = event.target;
    if (el.classList.contains('body-file-display')) {
        navigator.clipboard.writeText(el.getAttribute('title'));
    }
    if(el.classList.contains('filter-button')) {
        var filterQuery = '',
            message = el.closest('.message'),
            subject = el.classList.contains('topic')
                ? 'topic'
                : 'emote';
        if(message && filter[subject]  === message.dataset[subject]) {
            return;
        }
        filter[subject] = message
            ? message.dataset[subject]
            : '';
        for(filterSubject in filter) {
            if(filter[filterSubject] === '') {
                continue;
            }
            filterQuery += '[data-' +  filterSubject
                + filterOperator[filterSubject]
                + '"' + filter[filterSubject] + '"]';
        }
        document.getElementById('filtering').innerHTML = filterQuery === ''
            ? ''
            : '.message:not(' + filterQuery + ') { display: none; }';
        document.querySelector('.header-filter .' + subject).textContent = message
            ? filter[subject]
            : '';
    }
});
setTimeout(function() {
    document.body.classList.add('body--splash-in');
}, 1);