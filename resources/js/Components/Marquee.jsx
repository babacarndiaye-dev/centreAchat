export default function Marquee({ items }) {
    const track = [...items, ...items];

    return (
        <div className="overflow-hidden bg-terroir-dark py-3">
            <div className="marquee-track flex w-max items-center gap-10 whitespace-nowrap">
                {track.map((item, i) => (
                    <span key={i} className="flex items-center gap-10 text-xs font-semibold uppercase tracking-[0.2em] text-terroir-gold">
                        {item}
                        <span className="text-terroir-gold/40">×</span>
                    </span>
                ))}
            </div>
        </div>
    );
}
