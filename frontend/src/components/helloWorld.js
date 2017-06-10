export function helloWorld () {
    let element = document.createElement('div');

    element.innerHTML = ['Hello', 'world!'].join(', ');

    return element;
}
