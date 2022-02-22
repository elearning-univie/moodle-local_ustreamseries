define([], function () {
    return {
         init : function(html)  {
            let div = document.createElement('DIV');
            div.innerHTML = html;
            div = div.children[0];
            let maincontent = document.getElementById('maincontent');
            maincontent.insertAdjacentElement('afterend', div);
            return;
        }
    };
});