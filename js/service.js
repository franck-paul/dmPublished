/*global $, dotclear */
'use strict';

dotclear.dmPublishedPostsCount = function() {
  $.get('services.php', {
      f: 'dmPublishedPostsCount',
      xd_check: dotclear.nonce,
    })
    .done(function(data) {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // console.log($('rsp',data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const nb = $('rsp>count', data).attr('nb');
        // Badge on module
        dotclear.badge(
          $('#published-posts'), {
            id: 'dmrp',
            value: nb,
            remove: (nb == 0),
            type: 'soft',
          }
        );
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
    })
    .always(function() {
      // Nothing here
    });
};

dotclear.dmPublishedPostsView = function(line, action, e) {
  action = action || 'toggle';
  if ($(line).attr('id') == undefined) {
    return;
  }

  const postId = $(line).attr('id').substr(4);
  const lineId = `dmrpe${postId}`;
  let li = document.getElementById(lineId);

  if (!li) {
    // Get content
    dotclear.getEntryContent(postId, function(content) {
      if (content) {
        li = document.createElement('li');
        li.id = lineId;
        li.className = 'expand';
        $(li).append(content);
        $(line).addClass('expand');
        line.parentNode.insertBefore(li, line.nextSibling);
      } else {
        $(line).toggleClass('expand');
      }
    }, {
      clean: (e.metaKey),
      length: 300
    });
  } else {
    $(li).toggle();
    $(line).toggleClass('expand');
  }
};

$(function() {
  $.expandContent({
    lines: $('#published-posts li.line'),
    callback: dotclear.dmPublishedPostsView
  });
  $('#published-posts ul').addClass('expandable');
  // First pass
  dotclear.dmPublishedPostsCount();
  // Then fired every 300 seconds - 5 minutes
  dotclear.dbPublishedPostsCount_Timer = setInterval(dotclear.dmPublishedPostsCount, 300 * 1000);
});
