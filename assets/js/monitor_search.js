/*
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/

    http://www.atworkz.de
       info@atworkz.de
________________________________________
      Screenly OSE Monitor
________________________________________
*/

if ( window["Bloodhound"] ) {
  var somoComponents = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('tokens'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: {
      url: 'assets/php/search.json.php',
      cache: false,
    }
  });

  $('#somo_search input').typeahead(null, {
    name: 'somo-components',
    display: '',
    source: somoComponents,
    templates: {
      suggestion: function(data) {
        return '<a href="'+ location.origin +'/'+ data.url +'"><h4 class="mb-1">'+ data.title +'</h4><small>'+ data.description +'</small></a>';
      }
    }
  });

  $('#somo_search input').bind('typeahead:select', function(ev, data) {
    window.location.href = location.origin +'/'+ data.url;
  });
}
