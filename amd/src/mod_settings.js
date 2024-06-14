export const init = (data) => {

    if (data['allowinstancesettings'] == false) {
        document.querySelector('#id_openaichatsettings').style.display = "none";
        document.querySelector('#id_assistantheading').style.display = "none";
        document.querySelector('#id_chatheading').style.display = "none";
        document.querySelector('#id_advanced').style.display = "none";
    } else {
        //at the first loading of the page.
        switch_type_display();
        document.querySelector('#id_type').addEventListener('change', e => {
            switch_type_display();
        })

        $("#id_apikey").change(function() {
            var apikey = $(this).val()
            $.ajax({
                method: "POST",
                url: `${M.cfg.wwwroot}/mod/openaichat/api/get_assistants.php`,
                data: {"apikey" : apikey},
                dataType: "text",
                success: function(result){
                    //here do what I want
                    $("#id_assistant").empty();
                    let object = JSON.parse(result)
                    if($.isEmptyObject(object['error'])) {
                        $.each(object['data'], function( index, value ){
                            $('#id_assistant').append($('<option>', {
                                value: value['id'],
                                text: value['name']
                            }));
                        });
                    } else {
                        alert("Invalid API key!")
                    }
                }
            })
        })

        function switch_type_display() {
            if (document.querySelector('#id_type').value == "chat") {
                document.querySelector('#id_assistantheading').style.display = "none";
                document.querySelector('#id_chatheading').style.display = "block";
                document.querySelector("#fitem_id_model").style.display = "flex";
                document.querySelector("#fitem_id_temperature").style.display = "flex";
                document.querySelector("#fitem_id_maxlength").style.display = "flex";
                document.querySelector("#fitem_id_topp").style.display = "flex";
                document.querySelector("#fitem_id_frequency").style.display = "flex";
                document.querySelector("#fitem_id_presence").style.display = "flex";
                document.querySelector('#id_advanced').style.display = "block";
            } else {
                document.querySelector('#id_chatheading').style.display = "none";
                document.querySelector('#id_assistantheading').style.display = "block";
                document.querySelector("#fitem_id_model").style.display = "none";
                document.querySelector("#fitem_id_temperature").style.display = "none";
                document.querySelector("#fitem_id_maxlength").style.display = "none";
                document.querySelector("#fitem_id_topp").style.display = "none";
                document.querySelector("#fitem_id_frequency").style.display = "none";
                document.querySelector("#fitem_id_presence").style.display = "none";
                document.querySelector('#id_advanced').style.display = "none";
            }
        }
    }
}