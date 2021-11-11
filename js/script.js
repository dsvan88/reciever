const debug = false;
const blocksWithAction = document.body.querySelectorAll('*[data-action]');
blocksWithAction.forEach(block => block.addEventListener('click', (event) => mainFunc[camelize(block.dataset.action)](event)));

const blocksWithChangeAction = document.body.querySelectorAll('*[data-action-change]');
blocksWithChangeAction.forEach(block => block.addEventListener('change', (event) => mainFunc[camelize(block.dataset.actionChange)](event)));

const dashboardItems = document.body.querySelectorAll('.left-side li.dashboard__item');
const searchLocation = `${window.location.pathname}${window.location.search}`;
dashboardItems.forEach(item => item.querySelector('a').href === window.location.href ? item.classList.add('active') : false );