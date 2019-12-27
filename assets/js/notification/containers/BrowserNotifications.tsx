import {ReactElement} from 'react';
import {useAsyncEffect, useFetch} from '../../common/hooks';

export default function BrowserNotifications(): ReactElement | null {
    const fetch = useFetch();

    useAsyncEffect(async () => {
        try {
            navigator.serviceWorker.register('/sw.js');
            const sw           = await navigator.serviceWorker.ready;
            const subscription = await sw.pushManager.subscribe();

            fetch(
                '/browser_notification_subscriptions',
                {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/ld+json',
                        'Content-Type': 'application/json',
                    },
                    body:   JSON.stringify({endpoint: subscription.endpoint}),
                },
            );
        } catch (e) {
        }
    }, []);

    return null;
}
