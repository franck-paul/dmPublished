/*global $, dotclear */
'use strict';

dotclear.dmPublishedPostsCount = () => {
  dotclear.services(
    'dmPublishedPostsCount',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            const nb = response.payload.nb;
            // Badge on module
            dotclear.badge($('#published-posts'), {
              id: 'dmrp',
              value: nb,
              remove: nb !== undefined && nb === 0,
              type: 'soft',
            });
          }
        } else {
          console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
          return;
        }
      } catch (e) {
        console.log(e);
      }
    },
    (error) => {
      console.log(error);
    },
    true, // Use GET method
    { json: 1 },
  );
};

dotclear.dmPublishedPostsView = (line, action = 'toggle', e = null) => {
  if ($(line).attr('id') == undefined) {
    return;
  }

  const postId = $(line).attr('id').substr(4);
  const lineId = `dmrpe${postId}`;
  let li = document.getElementById(lineId);

  if (li) {
    $(li).toggle();
    $(line).toggleClass('expand');
  } else {
    // Get content
    dotclear.getEntryContent(
      postId,
      (content) => {
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
      },
      {
        clean: e.metaKey,
        length: 300,
      },
    );
  }
};

$(() => {
  $.expandContent({
    lines: $('#published-posts li.line'),
    callback: dotclear.dmPublishedPostsView,
  });
  $('#published-posts ul').addClass('expandable');
  // First pass
  dotclear.dmPublishedPostsCount();
  // Then fired every 300 seconds - 5 minutes
  dotclear.dbPublishedPostsCount_Timer = setInterval(dotclear.dmPublishedPostsCount, 300 * 1000);
});
