document.querySelectorAll('.playlist-card').forEach(element => {
    element.addEventListener('click', function() {
        const overlay = document.querySelector('#playlist-overlay');
        overlay.classList.toggle('hidden');
        const iframe = this.getAttribute('data-iframe');
        overlay.querySelector('#playlist-container').innerHTML = iframe;
    });
});

document.querySelector('#playlist-overlay').addEventListener('click', function() {
    const overlay = document.querySelector('#playlist-overlay');
    overlay.classList.add('hidden');
    overlay.querySelector('#playlist-container').innerHTML = '';
    console.log('close overlay');
});
  