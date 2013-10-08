;(function ($, window, document, undefined){

	$(document).ready(function($){

		// Setup sorting
		$("#tab-header").sortable({
			update: function(event, ui){
				savePosition();
			}
		});

		// Setup event listeners
		$(".tab").on("click", function(e){
			var $tab = $(e.currentTarget);
			if (!$tab.hasClass("active")){
				$(".tab").removeClass("active");
				$(".tab-content").removeClass("active");
				$tab.addClass("active");

				var $active_content = $(".tab-content[data-tab='" + $tab.attr("data-tab") + "']");
				$active_content.addClass("active");

				// Set iframe height to overcome tinymce bug
	            $active_content.find('iframe').css('height', '400px');
			}
		});
		$(".delete-tab-button").on("click", function(e){
			e.preventDefault();
			var tab_id = $(this).attr("data-id");
			if (confirm("Do you really want to delete this tab?")){
				$.post(ajaxurl, {action: "delete_tab", tab_id: tab_id }, function(response){
					if (response == 200){
						$("#tab-"+ tab_id).remove();
						$("#tab-content-"+ tab_id).remove();
						$(".tab:first-child").addClass("active");
						$(".tab-content:first-child").addClass("active");

						savePosition();
					} else {
						alert("An error occured and the tab could not be deleted..");
					}
				});
			}
		});
		$(".update-tab-button").on("click", function(e){
			tinyMCE.triggerSave();
			var tab_id = $(this).attr("data-id"),
				tiny_id = "tab"+ tab_id +"body"
				data = {
					action: "update_tab",
					tab_id: tab_id,
					title: $("#tab-"+ tab_id +"-title").val(),
					body: (tinymce.get(tiny_id)) ? tinymce.get(tiny_id).getContent() : $("#"+ tiny_id).val()
				};

			$.post(ajaxurl, data, function(response){
				if (response == 200){
					$("#tab-"+ data.tab_id +"-updated").fadeIn("slow", function(){
						$("#tab-"+ data.tab_id +"-updated").fadeOut("slow");
					});
				} else {
					alert("An error occured and the tab was not updated.. Please try again..");
				}
			});
		});

		function savePosition(){
			var ids = [];
			$("#tab-header .tab").each(function(index){
				ids.push( $(this).attr("data-id") );
			});
			$.post(ajaxurl, {action: "update_tab_position", ids: ids.join(",") }, function(response) {
				if (response != 200){
					alert("An error occured and the new position could not be saved..");
				}
			});
		}




		// Setup tab container form event
		$("#form-update-wptabs-settings").submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			$("#wptabs-update-settings-submit").attr("disabled", "disabled");
			$("#wptabs-update-settings-saving").show();

			var data = {
				action: "update_tab_container_settings",
				tab_container_id: tab_container_id,
				title: $("#tab-container-title").val()
			};

			$.post(ajaxurl, data, function(response) {
				$("#wptabs-update-settings-submit").removeAttr("disabled");
				$("#wptabs-update-settings-saving").fadeOut();
				if (response != 200){
					alert("An error occured and the setting was not updated.. Please try again..");
				}
			});
		});

	});

}(jQuery, window, document));