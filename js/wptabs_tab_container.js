;(function ($, window, document, undefined) {

	$(document).ready(function($){

		$(".tabcontainer").each(function(e){
			var $container = $(this);
			$container.find(".tab").on("click", function(e){
				var $tab = $(this);
				if (!$tab.hasClass("active")){
					$container.find(".tab").removeClass("active");
					$container.find(".tab-content").removeClass("active");
					$tab.addClass("active");
					$container.find(".tab-content[data-tab='" + $tab.attr("data-tab") + "']").addClass("active");
				}
			});
		});


	});

}(jQuery, window, document));