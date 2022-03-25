define([], function () {
    return {
         init : function(html, fullname, pluginname)  {
            let heading = document.getElementsByClassName('page-header-headings')[0].getElementsByTagName('h1');
            heading[0].innerHTML = fullname;

            let manageserieslink = document.createElement('DIV');
            manageserieslink.innerHTML = html;
            manageserieslink = manageserieslink.children[0];
            let maincontent = document.getElementById('maincontent');
            maincontent.insertAdjacentElement('afterend', manageserieslink);

            let heading2 = document.createElement('h2');
            heading2.innerHTML = pluginname;
            maincontent.insertAdjacentElement('afterend', heading2);

            return;
        }
    };
});