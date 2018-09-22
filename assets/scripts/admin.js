(function ($) {
  $(document).ready(function () {

    var indexingOptions = $('.indexing-options')
    var startIndexing = $('.start-indexing')
    var resumeIndexing = $('.resume-indexing')

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
