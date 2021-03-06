export function toDMY(date: Date, separator = '/'): string {
  return [
    date.getDate().toString().padStart(2, '0'),
    (date.getMonth() + 1).toString().padStart(2, '0'),
    date.getFullYear(),
  ].join(separator);
}

export function toReadableDate(date: Date): string {
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'long', day: 'numeric' };

  return new Intl.DateTimeFormat('fr-FR', options).format(date);
}
