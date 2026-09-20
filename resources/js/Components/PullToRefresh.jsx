import { useEffect, useRef, useState } from 'react';
import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';

const PULL_THRESHOLD = 70;
const MAX_PULL = 110;

export default function PullToRefresh({ children }) {
    const [pullDistance, setPullDistance] = useState(0);
    const [refreshing, setRefreshing] = useState(false);
    const touchStartY = useRef(0);
    const pulling = useRef(false);
    const refreshingRef = useRef(false);
    const pullDistanceRef = useRef(0);

    useEffect(() => {
        function reset() {
            pulling.current = false;
            pullDistanceRef.current = 0;
            setPullDistance(0);
        }

        function onTouchStart(e) {
            if (window.scrollY > 0 || refreshingRef.current) return;
            touchStartY.current = e.touches[0].clientY;
            pulling.current = true;
        }

        function onTouchMove(e) {
            if (!pulling.current || refreshingRef.current) return;
            const delta = e.touches[0].clientY - touchStartY.current;
            if (delta <= 0 || window.scrollY > 0) {
                reset();
                return;
            }
            const next = Math.min(delta * 0.5, MAX_PULL);
            pullDistanceRef.current = next;
            setPullDistance(next);
        }

        function onTouchEnd() {
            if (!pulling.current) return;
            pulling.current = false;

            if (pullDistanceRef.current >= PULL_THRESHOLD && !refreshingRef.current) {
                refreshingRef.current = true;
                setRefreshing(true);
                router.reload({
                    onFinish: () => {
                        refreshingRef.current = false;
                        setRefreshing(false);
                        pullDistanceRef.current = 0;
                        setPullDistance(0);
                    },
                });
            } else {
                pullDistanceRef.current = 0;
                setPullDistance(0);
            }
        }

        window.addEventListener('touchstart', onTouchStart, { passive: true });
        window.addEventListener('touchmove', onTouchMove, { passive: true });
        window.addEventListener('touchend', onTouchEnd, { passive: true });
        return () => {
            window.removeEventListener('touchstart', onTouchStart);
            window.removeEventListener('touchmove', onTouchMove);
            window.removeEventListener('touchend', onTouchEnd);
        };
    }, []);

    const indicatorHeight = refreshing ? 52 : pullDistance;

    return (
        <div style={{ overscrollBehaviorY: 'contain' }}>
            <div
                className="flex items-center justify-center overflow-hidden"
                style={{ height: indicatorHeight, transition: pullDistance === 0 || refreshing ? 'height 0.2s ease' : 'none' }}
            >
                <motion.span
                    animate={refreshing ? { rotate: 360 } : { rotate: pullDistance * 3 }}
                    transition={refreshing ? { repeat: Infinity, duration: 0.7, ease: 'linear' } : { duration: 0 }}
                    className="material-symbols-outlined text-2xl text-terroir-green"
                    style={{ opacity: refreshing ? 1 : Math.min(pullDistance / PULL_THRESHOLD, 1) }}
                >
                    refresh
                </motion.span>
            </div>
            {children}
        </div>
    );
}
