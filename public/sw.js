self.onpush = function onPush(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data.json();
    self.registration.showNotification('Heimdall', {
        body: data.subject,
        tag: 'simple-push-demo-notification',
        icon: null
    });
};
