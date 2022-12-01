$("#stationNumber").bind('input', function() {
    var number = $("#stationNumber").val();
    $.get("api.php?stationNumber="+number+"", function(data) {
        var station = jQuery.parseJSON(data);
        if(station.name == null) {
            $("#stationName").val("");
        } else {
            $("#stationName").val(station.name);
        }
    });
});

$("#addRow").click(function() {
    $("#riskEvalution").clone().insertAfter("#riskEvalution");
});

$("#addName").click(function() {
    if($("#names").val() != "disabled") {
        if($("#montorer").val() == "") {
            $("#montorer").val($("#names").val());
            $("#names option:selected").remove();
        } else {
            $("#montorer").val($("#montorer").val() + ", " + $("#names").val());
            $("#names option:selected").remove();
        }

        if($("#names").val() == null) {
            $("#names").append('<option value="disabled">Ikke flere navne</option>');
            $('#names').attr('disabled', 'disabled');
        }
    }
});