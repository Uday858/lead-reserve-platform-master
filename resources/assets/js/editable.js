window.onload = function() {
    (function($) {
        $(document).ready(function(){

            /**
             * Go ahead and initate the bootstrap tooltip
             */
            $('[data-toggle="tooltip"]').tooltip();

            /**
             * For Creating New Posting Param.
             * */
            $("a.postingParamSwitcher").click(function(e){
               $("input#paramTypeField").val($(this).attr("data-type"));
            });

            /**
             * For Posting Params...
             */
            $("button#add-new-posting-param").click(function(e) {
                $("form#posting-param-form table").append($("script#posting-param-input-template").html());
                registerClickEventsForEditableTextFields();
            });

            function registerClickEventsForEditableTextFields() {
                $("tr.editable-table-item button.remove-input-field").click(function(){
                    var EditableTableItem = $(this).closest("tr.editable-table-item");
                    if(EditableTableItem.hasClass("new")) {
                        EditableTableItem.remove();
                    } else {
                        $(this).closest("tr.editable-table-item").addClass("hidden");
                        $(this).closest("tr.editable-table-item").find("input.posting-action").val("remove");
                    }
                });
            } registerClickEventsForEditableTextFields();

            // For form submission.
            $("button#submit-posting-param-form").click(function(e) {
                $("form#posting-param-form")[0].submit();
            });

            /**
             * For Test Link Generation...
             */
            $("button#test-link-generate-button").click(function() {
                var inputFields = [];
                $("div.test-link-form input").each(function(index,item){
                    inputFields.push([item.name, item.value]);
                });
                console.log(inputFields);
                var sourceURL = new URL($("span.test-link-source")[0].innerText);
                sourceURL.searchParams.forEach(function(value,key){
                    inputFields.forEach(function(field){
                        if(key.indexOf(field[0])!=-1) {
                            sourceURL.searchParams.set(key,field[1]);
                        } else {
                        }
                    });
                });
                $("textarea#test-link-output").val(sourceURL.href);
            });

            /**
             * For Editing Campaign Attributes
             */
            if($("select#edit-campaign-campaign-type")[0]!=undefined) {
                $("select#edit-campaign-campaign-type")[0].value = $("div#edit-campaign-campaign-type-value").attr("data-campaign-type-id");
            }

            /**
             * For Creating Campaigns
             */
            if($("select#create-campaign-advertiser-select")[0]!=undefined) {
                $("select#create-campaign-advertiser-select")[0].value = $("div#create-campaign-advertiser-value").attr("data-advertiser-id");
            }
        });
    })(jQuery);
}