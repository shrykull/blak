$(document).ready(function() {
  $('#searchString').autocomplete({
    source: function (request, response) {
        var query = request.term.split(/,\s*/).pop();
        $.getJSON(
            "http://www.gw2spidy.com/api/v0.9/json/item-search/" + query,
            function (data) {
                var resultArray = [];
                $.each(data['results'], function (i) {
                    resultArray.push({
                        label: data['results'][i]['name'],
                        value: data['results'][i]['data_id']
                    });
                });
                response(resultArray);
            }
        );
    },
    minLength: 3,
     focus: function() {
      // prevent value inserted on focus (e.g. keyboad navigation)
      return false;
    },
    select: function (event, ui) {
        var terms = this.value.split(/,\s*/);
        terms.pop();
        terms.push(ui.item.value);
        terms.push("");
        this.value = terms.join(", ");
        return false;
    }
  });
});