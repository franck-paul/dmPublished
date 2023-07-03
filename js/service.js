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
            const { nb } = response.payload;
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

dotclear.dmLastPublishedRows = () => {
  dotclear.services(
    'dmPublisheduledRows',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            // Replace current list with the new one
            if ($('#published-posts ul').length) {
              $('#published-posts ul').remove();
            }
            if ($('#published-posts p').length) {
              $('#published-posts p').remove();
            }
            // Display module content
            $('#published-posts h3').after(response.payload.list);
            // Bind every new lines for viewing published post content
            $.expandContent({
              lines: $('#published-posts li.line'),
              callback: dotclear.dmPublishedPostsView,
            });
            $('#published-posts ul').addClass('expandable');
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

dotclear.dmPublishedCheck = () => {
  dotclear.services(
    'dmPublishedCheck',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            dotclear.dmLastPublishedRows();
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
          return;
        }
        $(line).toggleClass('expand');
      },
      {
        clean: e.metaKey,
        length: 300,
      },
    );
  }
};

$(() => {
  Object.assign(dotclear, dotclear.getData('dm_published'));
  $.expandContent({
    lines: $('#published-posts li.line'),
    callback: dotclear.dmPublishedPostsView,
  });
  $('#published-posts ul').addClass('expandable');
  if (dotclear.dmPublished_Monitor) {
    // Auto refresh requested : Set interval between two checks for publishing published entries
    dotclear.dmPublished_Timer = setInterval(dotclear.dmPublishedCheck, (dotclear.dmPublished_Interval || 300) * 1000);
  }
  // First pass
  dotclear.dmPublishedPostsCount();
  // Then fired every x seconds
  dotclear.dbPublishedPostsCount_Timer = setInterval(
    dotclear.dmPublishedPostsCount,
    (dotclear.dmPublished_Interval || 300) * 1000,
  );
});
