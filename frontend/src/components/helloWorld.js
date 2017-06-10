module.exports = function component () {
    var element = document.createElement('div');

    element.innerHTML = ['Hello', 'world!'].join(', ');

    return element;
};
