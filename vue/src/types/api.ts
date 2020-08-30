export type PaginationInterface<T> = {
  results: T[];
  pagination: {
    currentPage: number;
    maxPage: number;
    perPage: number;
    total: number;
  };
}

export interface PictureRange {
  results: Record<number, string>;
  total: number;
}
