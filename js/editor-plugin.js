(function($) {

	tinymce.create('tinymce.plugins.WpTabsEmbedder', {
		init : function(ed, url) {

			ed.addButton('wptabs', {
				title : 'Tabs Embedder',
				image : url + '/../img/tinymce-button.png',
				onclick : function(){

					var wptabs_list_popup = $("<div>").attr("id", "wptabs-embedder-list").text("Loading, please wait...");
					$("body").append(wptabs_list_popup);

					wptabs_list_popup.load(ajaxurl, {
							action: "wptabs_list"
						}, function(){

							$(".wptab-link", wptabs_list_popup).on("click", function(e){
								var wptabs_id = $(this).attr("data-id");
								e.preventDefault();
								ed.execCommand('mceInsertContent', false, '[TABS '+ wptabs_id +']');
								wptabs_list_popup.remove();
								wptabs_list_popup = null;
							});

							$(".close", wptabs_list_popup).on("click", function(e){
								e.preventDefault();
								wptabs_list_popup.remove();
								wptabs_list_popup = null;
							});

						}
					);
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Embeddable tabs button",
				author : 'Christian Wannerstedt',
				authorurl : 'http://www.kloon.se/',
				infourl : 'http://www.kloon.se/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wptabs', tinymce.plugins.WpTabsEmbedder);
})(jQuery);
