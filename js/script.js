const debug = false;
const blocksWithAction = document.body.querySelectorAll('*[data-action]');
blocksWithAction.forEach(block => block.addEventListener('click', (event)=> mainFunc[camelize(block.dataset.action)](event)));