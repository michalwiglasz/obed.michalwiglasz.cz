document.addEventListener("DOMContentLoaded", function(event) {

    var elements = document.getElementsByClassName("tmi-daily");
    var hash = {};

    function setHash() {
        var hashstr = [];
        var empty = true;
        for (var key in hash) {
          if (hash.hasOwnProperty(key) && hash[key] > 0) {
            hashstr.push(key);
            empty = false;
          }
        }

        if (hashstr.length) {
            window.location.hash = hashstr.join(',');
        } else {
            history.pushState([], "", ".");
        }
        //history.pushState(hashstr, '', '#' + hashstr.join(','));
    }

    function loadHash() {
        if (history.state) {
            hashstr = history.state;
        } else {
            hashstr = window.location.hash.replace(/^#/, '').split(',');
        }

        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            el.classList.remove('highlight1');
        }

        for (var i = 0; i < hashstr.length; i++) {
            var num = parseInt(hashstr[i]);
            if (num) {
                elements[num].classList.add('highlight1');
                hash[num] = 1;
            }
        }
    }

    function clearSelection() {
        if(document.selection && document.selection.empty) {
            document.selection.empty();
        } else if(window.getSelection) {
            var sel = window.getSelection();
            sel.removeAllRanges();
        }
    }

    for (var i = 0; i < elements.length; i++) {
        var el = elements[i];
        el.dataset.num = i;
        hash[i] = 0;
        el.addEventListener('dblclick', function(e) {
            /*if (!e.ctrlKey) {
                return;
            }*/
            e.preventDefault();
            num = parseInt(this.dataset.num);
            if (this.classList.contains('highlight1')) {
                this.classList.remove('highlight1');
                hash[num] = 0;
            } else {
                this.classList.add('highlight1');
                hash[num] = 1;
            }
            setHash();
            clearSelection();
        });
    }

    window.addEventListener('popstate', function(e) {
        loadHash();
    });

    window.onbeforeunload = function(e) {
        window.location.hash = '';
    };

    loadHash();
    history.replaceState([], "", ".");
});
