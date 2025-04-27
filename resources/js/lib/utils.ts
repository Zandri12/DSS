import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}
interface ValueUpdaterRef<T> {
    value: T;
}

type UpdaterOrValue<T> = T | ((currentValue: T) => T);

export function valueUpdater<T>(updaterOrValue: UpdaterOrValue<T>, ref: ValueUpdaterRef<T>): void {
    ref.value = typeof updaterOrValue === 'function' 
      ? (updaterOrValue as (currentValue: T) => T)(ref.value) 
      : updaterOrValue;
}