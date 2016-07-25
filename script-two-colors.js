document.addEventListener("DOMContentLoaded", function(event) {

    var elements = document.getElementsByClassName("tmi-daily");
    var hash = {};

    function setHash() {
        var hashstr = [[], []];
        var empty = true;
        for (var key in hash) {
          if (hash.hasOwnProperty(key) && hash[key]) {
            hashstr[hash[key] - 1].push(key);
            empty = false;
          }
        }
        if(empty) {
            window.location.hash = '';
        } else {
            window.location.hash = hashstr[0].join(',') + ':' + hashstr[1].join(',');
        }
    }

    function loadHash() {
        hashstr = window.location.hash.replace(/^#/, '').split(':');
        hashstr[0] = hashstr[0].split(',');
        hashstr[1] = hashstr[1].split(',');
        console.log(hashstr);

        for (var i = 0; i < hashstr[0].length; i++) {
            var num = parseInt(hashstr[0][i]);
            console.log(num);
            elements[num].classList.add('highlight1');
            hash[num] = 1;
        }

        for (var i = 0; i < hashstr[1].length; i++) {
            var num = parseInt(hashstr[1][i]);
            elements[num].classList.add('highlight2');
            hash[num] = 2;
        }
    }

    for (var i = 0; i < elements.length; i++) {
        var el = elements[i];
        el.dataset.num = i;
        hash[i] = 0;
        el.addEventListener('click', function(e) {
            console.log(el);
            num = parseInt(this.dataset.num);
            e.preventDefault();
            if (this.classList.contains('highlight1')) {
                this.classList.remove('highlight1');
                this.classList.add('highlight2');
                hash[num] = 2;
            } else if (this.classList.contains('highlight2')) {
                this.classList.remove('highlight2');
                hash[num] = 0;
            } else {
                this.classList.add('highlight1');
                hash[num] = 1;
            }

            setHash();
        });
    }

    loadHash();
});
