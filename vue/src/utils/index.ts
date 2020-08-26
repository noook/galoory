export function randomId(): string {
  return `_${Math.random().toString(36).substr(2, 9)}`;
}

export async function sleep(duration: number): Promise<void> {
  return new Promise((resolve) => setTimeout(() => {
    resolve();
  }, duration));
}
