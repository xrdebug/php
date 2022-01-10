var filter = {
    topic: '',
    emote: '',
},
filterOperator = {
    topic: '=',
    emote: '*=',
},
es = new EventSource('dump');
es.addEventListener('message', function (event) {
    var data = JSON.parse(event.data),
    template = document.querySelector('#message'),
    el = template.content.cloneNode(true);
    el.querySelector('.time').textContent = (new Date()).toTimeString().split(' ')[0];
    el.querySelector('.topic').textContent = data.topic;
    el.querySelector('.emote').textContent = data.emote;
    el.querySelector('.body-raw').innerHTML = data.message;
    var bodyFileDisplay = el.querySelector('.body-file-display');
    bodyFileDisplay.textContent = data.file_display_short;
    bodyFileDisplay.setAttribute('title', data.file_display);
    document.body.prepend(el);
    el = document.querySelector('.message');
    el.dataset.emote = data.emote;
    el.dataset.topic = data.topic;
});
document.querySelector('.header-title').addEventListener('input', event => {
    document.title = event.target.textContent;
});
document.addEventListener('click', event => {
    var el = event.target;
    if (el.classList.contains('body-file-display')) {
        navigator.clipboard.writeText(el.getAttribute('title'));
    }
    if(el.classList.contains('filter-button')) {
        var filterQuery = '',
            hasFilter = false,
            message = el.closest('.message'),
            header = el.closest('header'),
            resetFilter = header !== null,
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
            resetFilter = false;
            filterQuery += '[data-' +  filterSubject + filterOperator[filterSubject] + '"' + filter[filterSubject] + '"]';
        }
        document.getElementById('filtering').innerHTML = filterQuery === ''
            ? ''
            : '.message:not(' + filterQuery + ') { display: none; }';
        document.querySelector('.header-filter .' + subject).textContent = message
            ? filter[subject]
            : '';
    }
});