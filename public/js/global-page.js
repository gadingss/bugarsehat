'use strict';
function ribuanWithComma(obj) {
    let firstObj=obj.split(',',2);
    let lastComma="";
    if(firstObj.length>1){
        lastComma=","+firstObj[1].toString().substr(0,2);
    }
    let b = firstObj[0].toString(),
        c = "",
        panjang = b.length,
        j = 0;
    for (let i = panjang; i > 0; i--) {
        j = j + 1;
        if (((j % 3) == 1) && (j != 1)) {
            c = b.substr(i - 1, 1) + "." + c;
        } else {
            c = b.substr(i - 1, 1) + c;
        }
    }
    return c+lastComma;
};

function ribuan(obj) {
    let firstObj=obj;
    let b = firstObj.toString(),
        c = "",
        panjang = b.length,
        j = 0;
    for (let i = panjang; i > 0; i--) {
        j = j + 1;
        if (((j % 3) == 1) && (j != 1)) {
            c = b.substr(i - 1, 1) + "." + c;
        } else {
            c = b.substr(i - 1, 1) + c;
        }
    }
    return c;
};

function formatNumber(obj) {
    let firstObj=obj;
    let b = firstObj.toString(),
        c = "",
        panjang = b.length,
        j = 0;
    for (let i = panjang; i > 0; i--) {
        j = j + 1;
        c = b.substr(i - 1, 1) + c;
    }
    return c;
};
KTApp.showPageLoading();
// KTApp.hidePageLoading();