define([], function () {
    return {
         init : function()  {
            let actionselect = document.getElementById('id_action');
            let linkalertpossible = document.getElementById('linkalertpossible');
            let changehead = function changehead() {
                if(actionselect.value == 'link') {
                    if(linkalertpossible) {
                        linkalertpossible.classList.remove("hidden");
                    }
                }
                if(actionselect.value == 'create') {
                    if(linkalertpossible) {
                        linkalertpossible.classList.add("hidden");
                    }
                }
            };

            actionselect.onchange = changehead;
            changehead();
            return;
        }
    };
});