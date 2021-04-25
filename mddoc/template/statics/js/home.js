$.ajaxSetup({
	xhrFields: {
		withCredentials: true
	},
});
$(function(){
	layui.element.init();
	layui.table.init();
	layui.form.render();
});