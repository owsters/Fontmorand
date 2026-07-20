document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.film-plate').forEach(function (plate) {
    var play = plate.querySelector('.film-play');
    var poster = plate.querySelector('.film-poster');
    var id = plate.getAttribute('data-video');
    if (!play || !poster || !id) return;
    play.addEventListener('click', function () {
      var iframe = document.createElement('iframe');
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1';
      iframe.title = 'Fontmorand drone film';
      iframe.allow = 'autoplay; encrypted-media; fullscreen';
      iframe.setAttribute('allowfullscreen', '');
      poster.replaceWith(iframe);
    });
  });
});
