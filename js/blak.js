var ingredientIDs = { filter_ids: "24277, 24324, 19663, 20799" };
var outputIDs = { filter_ids: "24325" };

//TODO: Refactor: extract ingredientIDs and outputIDs to method head
function updateRecipe() {
  fetchStuff(ingredientIDs, document.getElementById('ingredientContent'));
  fetchStuff(outputIDs, document.getElementById('outputContent'));
  //console.log(document.getElementById('mainview').innerHTML);
}

function showRecipe() {
  document.getElementById('searchResults').style.display = 'none';
  document.getElementById('ingredientContent').style.display = 'block';
  document.getElementById('arrow').style.display = 'block';
  document.getElementById('outputContent').style.display = 'block';
}


function searchFor(searchString) {
  fetchStuff({ filter_ids: searchString}, document.getElementById('searchResults'));
}

function showSearchResults() {
  document.getElementById('searchResults').style.display = 'block';
  document.getElementById('ingredientContent').style.display = 'none';
  document.getElementById('arrow').style.display = 'none';
  document.getElementById('outputContent').style.display = 'none';
}


//should be private but js sucks
function fetchStuff(data, target) {
    var API = "http://www.gw2spidy.com/api/v0.9/json/items/all?";
  $.getJSON(
    // URL
    API,
    // data
    data,
    // success
    function(json) {
      //console.log(json);
      target.innerHTML = toTable(json.results);
    });
}

function toTable(results) {
  var html = "<table class=\"table table-striped table-responsive\">";
  var allowedKeys = ["name", "data_id", "img", "max_offer_unit_price", "min_sale_unit_price"];
  html = makeTableHeaders(html, results[0], allowedKeys);
  $.each(results, function(i, item) {
    html += "<tr>";
    $.each(item, function(key, value) {
      if ($.inArray(key, allowedKeys) > -1) {
        html += "<td>";
        if (key == "img") {
         html += "<img class=\"item-preview\" src=\"" + value + "\" />";
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
    if ($.inArray(k, allowedKeys) > -1) {
      if (k == "max_offer_unit_price")
        k = "max offer";
      if (k == "min_sale_unit_price")
        k = "min sale";
      html += "<th>" + k + "</th>";
    }
  });
  html += "</tr>";
  return html; //js macht kein call by reference -.-
}

$(document).ready(function() {
  // register actions
  $('#fetchStuff').click(function() {
    if (document.getElementById('searchResults').style.display === 'none') {
      updateRecipe();
      showRecipe(); 
    } else if ($('#searchString').val() !== '') {
      searchFor($('#searchString').val());
      showSearchResults();
    }
  });
  
  $('#btnSearch').click(function() {
    var searchString = $('#searchString').val();
    if (searchString.match(/^\d+(\,\s*\d*)*$/g)!==null) {
      //console.log(searchString);
      searchFor(searchString);
      showSearchResults();
    }
  });
  
  //initialize
  updateRecipe();
  showRecipe();
  
  // make the searchfield autogrowing
  // but first set max-width (60% of window width)
  $('#searchString').css('max-width', 0.6*$(window).width());
  $('#searchString').autoGrow();
});