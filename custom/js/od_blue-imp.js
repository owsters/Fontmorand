//for Blue-Imp

document.getElementById('links_ext').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_int').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_ob').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_pad').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
/*document.getElementById('links_stable').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_gc').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_lake').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_dp').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};*/