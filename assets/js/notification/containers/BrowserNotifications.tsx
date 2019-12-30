import {ReactElement} from 'react';
import {useAsyncEffect, useFetch} from '../../common/hooks';
import urlBase64ToUint8Array from '../../lib/urlBase64ToUint8Array';

export default function BrowserNotifications(): ReactElement | null {
    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            navigator.serviceWorker.register('/sw.js');
            const sw = await navigator.serviceWorker.ready;

            Notification.requestPermission(async function (status) {
                if ('granted' !== status) {
                    return;
                }

                const subscription = await sw.pushManager.subscribe({
                    userVisibleOnly:      true,
                    applicationServerKey: urlBase64ToUint8Array(require('../../../../config/vapid/public_key.txt')),
                });

                await fetch(
                    '/browser_notification_subscriptions',
                    {
                        method:  'POST',
                        headers: {
                            'Accept':       'application/ld+json',
                            'Content-Type': 'application/json',
                        },
                        body:    JSON.stringify(subscription.toJSON()),
                    },
                );
            });
        } catch (e) {
        }
    }, []);

    return null;
}
