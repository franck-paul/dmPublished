/*global dotclear */
'use strict';

dotclear.ready(() => {
  dotclear.dmPublished = dotclear.getData('dm_published');

  const viewPost = (line, _action = 'toggle', event = null) => {
    dotclear.dmViewPost(line, 'dmrpe', event.metaKey);
  };

  const getCount = () => {
    dotclear.services(
      'dmPublishedPostsCount',
      (data) => {
        try {
          const response = JSON.parse(data);
          if (response?.success) {
            if (response?.payload.ret) {
              const { nb } = response.payload;
              // Badge on module
              dotclear.badge(document.querySelector('#published-posts'), {
                id: 'dmrp',
                value: nb,
                remove: nb <= 0,
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

  const getRows = () => {
    dotclear.services(
      'dmPublisheduledRows',
      (data) => {
        try {
          const response = JSON.parse(data);
          if (response?.success) {
            if (response?.payload.ret) {
              // Replace current list with the new one
              for (const item of document.querySelectorAll('#published-posts ul')) item.remove();
              for (const item of document.querySelectorAll('#published-posts p')) item.remove();
              // Display module content
              const title = document.querySelector('#published-posts h3');
              title?.insertAdjacentHTML('afterend', response.payload.list);

              // Bind every new lines for viewing published post content
              dotclear.expandContent({
                lines: document.querySelectorAll('#published-posts li.line'),
                callback: viewPost,
              });
              for (const item of document.querySelectorAll('#published-posts ul')) item.classList.add('expandable');
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

  const check = () => {
    dotclear.services(
      'dmPublishedCheck',
      (data) => {
        try {
          const response = JSON.parse(data);
          if (response?.success) {
            if (response?.payload.ret) {
              getRows();
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

  dotclear.expandContent({
    lines: document.querySelectorAll('#published-posts li.line'),
    callback: viewPost,
  });
  for (const item of document.querySelectorAll('#published-posts ul')) item.classList.add('expandable');

  if (dotclear.dmPublished.monitor) {
    // Auto refresh requested : Set interval between two checks for publishing published entries
    dotclear.dmPublished.timer = setInterval(check, (dotclear.dmPublished.interval || 300) * 1000);
  }
  // First pass
  getCount();
  // Then fired every x seconds
  dotclear.dmPublished.timer = setInterval(getCount, (dotclear.dmPublished.interval || 300) * 1000);
});
