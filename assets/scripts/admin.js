(function ($) {
  $(document).ready(function () {

    var features = $('.wprds-feature')
    var indexingOptions = $('.indexing-options')
    var startIndexing = $('.start-indexing')
    var resumeIndexing = $('.resume-indexing')

    features.on( 'click', '.save-settings', function( event ) {
      event.preventDefault();
      var $this = $( this  )
      if ( $this.hasClass( 'disabled' ) ) {
        return;
      }
      var feature = $this.attr( 'data-feature' );
      var $feature = features.find( '.wprds-feature-' + feature );
  
      var settings = {};
  
      var $settings = $feature.find('.setting-field');
  
      $settings.each(function() {
        var type = $( this ).attr( 'type' );
        var name = $( this ).attr( 'data-field-name' );
        var value = $( this ).attr( 'value' );
        if ( 'radio' === type ) {
          if ( this.checked ) {
            settings[ name ] = value;
          }
        } else {
          settings[ name ] = value;
        }
      });
  console.log(settings)
      $this.parent().addClass( 'saving' );

      $.ajax( {
        method: 'post',
        url: ajaxurl,
        data: {
          action: 'wp_redisearch_save_feature',
          feature: feature,
          nonce: wpRds.nonce,
          settings: settings
        }
      } ).done( function( response ) {
        setTimeout( function() {
          $this.parent().removeClass( 'saving' );
  
          if ( '1' === settings.active ) {
            $feature.addClass( 'feature-active' );
          } else {
            $feature.removeClass( 'feature-active' );
          }
          
          if ( response.data.reindex ) { 
            dropIndex();
          }
        }, 700 );
      } ).error( function() {
        setTimeout( function() {
          $feature.removeClass( 'saving' );
          $feature.removeClass( 'feature-active' );
        }, 700 );
      } );
    });
    
    if (indexingOptions.attr('data-num-docs') == indexingOptions.attr('data-num-posts')) {
      resumeIndexing.css('display', 'none')
    }
    
    startIndexing.click(function (e) {
      e.preventDefault()
      startIndexing.css('display', 'none')
      dropIndex()
    })
    
    
    resumeIndexing.click(function (e) {
      e.preventDefault()
      resumeIndexing.css('display', 'none')
      addToIndex()
    })
      
    function dropIndex() {
      let postData = {
        action: 'wp_redisearch_drop_index'
      }
      jQuery.ajax({
        data: postData,
        type: "post",
        url: wpRds.ajaxUrl
      })
        .done(function (res) {
          if (res.data) {
            addToIndex()
          }
        })
        .error(function (err) {
          console.log(err)
        })
    }

    function addToIndex() {
      let postData = {
        action: 'wp_redisearch_add_to_index'
      }
      jQuery.ajax({
        data: postData,
        type: "post",
        url: wpRds.ajaxUrl
      })
        .done(function (res) {
          if (res.data.offset < res.data.found_posts) {
            updateProgressBar(res.data.offset, res.data.found_posts)
            addToIndex()
          } else {
            updateProgressBar(res.data.found_posts, res.data.found_posts)
            startIndexing.css('display', 'inline-block')
            writeToDisk()
          }
        })
        .error(function (err) {
          console.log(err)
        })
    }

    function writeToDisk() {
      let postData = {
        action: 'wp_redisearch_write_to_disk'
      }
      jQuery.ajax({
        data: postData,
        type: "post",
        url: wpRds.ajaxUrl
      })
        .done(function (res) {
          console.log('Data successfully written to the disk ', res.data)
        })
        .error(function (err) {
          console.log('Error writing to the disk ', err)
        })
    }

    function updateProgressBar(numDocs, numPosts) {
      let pBar = $("#indexBar")
      let statNumDoc = $('#statNumDoc')
      let statNumPosts = $('#statNumPosts')
      statNumDoc.text(numDocs)
      statNumPosts.text(numPosts)
      let width = (numDocs / numPosts) * 100
      pBar.css('width', width + '%')
    }

    renderProgressBar()
    function renderProgressBar() {
      let pBar = $("#indexBar")
      let numDocs = pBar.attr('data-num-docs')
      let numPosts = pBar.attr('data-num-posts')
      let width = (numDocs / numPosts) * 100
      pBar.css('width', width + '%')
    }
    
  });
} )( jQuery );
