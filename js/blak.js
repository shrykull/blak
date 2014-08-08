var ingredientIDs = { filter_ids: "24277, 24324, 19663, 20799" };
var outputIDs = { filter_ids: "24325" };

function refresh() {
  fetchIngredients(ingredientIDs);
  fetchOutput(outputIDs);
}

function fetchIngredients(data) {
  return fetchStuff(data, "#ingredients");
}
function fetchOutput(data) {
  return fetchStuff(data, "#output");
}

//should be private but js sucsk
function fetchStuff(data, target) {
    var API = "http://www.gw2spidy.com/api/v0.9/json/items/all?";
  $.getJSON(
    // URL
    API,
    // data
    data,
    // success
    function(json) {
      console.log(json);
      $(target).html(toTable(json.results));
    });
}
function toTable(results) {
  var html = "<table class=\"table table-striped table-responsive\">";
  var allowedKeys = ["data_id", "name", "img", "max_offer_unit_price", "min_sale_unit_price"];
  html = makeTableHeaders(html, results[0], allowedKeys);
  $.each(results, function(i, item) {
    html += "<tr>";
    $.each(item, function(key, value) {
      if ($.inArray(key, allowedKeys) > 0) {
        html += "<td>";
        if (key == "img") {
         html += "<img src='" + value + "'/>";
        } else{ 
          html += value;
        }
        html += "</td>";
      }
    });
    
    html += "</tr>";
  });
  html += "</table>";
  return html;
}

function makeTableHeaders(html, item, allowedKeys) {
  html += "<tr>";
  $.each(item, function(k, v) {
    if ($.inArray(k, allowedKeys) > 0) {
      html += "<th>" + k + "</th>";
    }
  });
  html += "</tr>";
  return html; //js macht kein call by reference -.-
}

$(document).ready(function() {
  // register actions
  $('#fetchStuff').click(function() {
    refresh();
  });
  
  $('#btnSearch').click(function() {
    var searchString = $('#searchString').val();
    if (searchString.match(/^\d+(\,\s*\d*)*$/g)!==null) {
      console.log(searchString);
      fetchStuff({ filter_ids: searchString}, "#mainview");
    }
  });
  
  //initialize
  refresh();

  // make the searchfield autogrowing
  // but first set max-width (60% of window width)
  $('#searchString').css('max-width', 0.6*$(window).width());
  $('#searchString').autoGrow();
});