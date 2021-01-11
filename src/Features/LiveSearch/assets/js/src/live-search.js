(function ($) {
  $(document).ready(function () {

    function autocomplete(input) {
      input.forEach(inp => {
        inp.setAttribute("autocomplete", "off");
        var currentFocus;
        inp.addEventListener("input", function(e) {
          var a, val = this.value;
          closeAllLists();
          if (!val) { return false;}
          currentFocus = -1;
          this.parentNode.style.position = "relative";
          this.parentNode.style.display = "block";
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "autocomplete-list");
          a.setAttribute("class", "autocomplete-items");
          this.parentNode.appendChild(a);
          getSuggestion(val, a);
        });
        inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
          } else if (e.keyCode == 38) {
            currentFocus--;
            addActive(x);
          } else if (e.keyCode == 13) {
            // e.preventDefault();
            if (currentFocus > -1) {
              if (x) x[currentFocus].click();
            }
          }
        });
        function addActive(x) {
          if (!x) return false;
          removeActive(x);
          if (currentFocus >= x.length) currentFocus = 0;
          if (currentFocus < 0) currentFocus = (x.length - 1);
          x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
          for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
          }
        }
        function closeAllLists(elmnt) {
          var x = document.getElementsByClassName("autocomplete-items");
          for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
              x[i].parentNode.removeChild(x[i]);
            }
          }
        }
        function getSuggestion(term, a) {
          let postData = {
            action: 'wp_redisearch_get_suggestion',
            term: term
          }
          jQuery.ajax({
            data: postData,
            type: "post",
            url: wpRds.ajaxUrl,
            success: function (data) {
              let results = data;
              let b;
              for (let i = 0; i < results.length; i = i + 2) {
                b = document.createElement("DIV");
                b.innerHTML = "<strong>" + results[i].post_title.substr(0, term.length) + "</strong>";
                b.innerHTML += results[i].post_title.substr(term.length);
                b.innerHTML += "<input type='hidden' value='" + results[i].post_title + "'>";
                b.addEventListener("click", function(e) {
                  inp.value = this.getElementsByTagName("input")[0].value;
                  inp.closest('form').submit();
                  closeAllLists();
                });
                a.appendChild(b);
              }
            }
          });
        }
        document.addEventListener("click", function (e) {
          closeAllLists(e.target);
        });
      });
    }

    autocomplete(document.querySelectorAll('[name="s"]'));

  });
} )( jQuery );
