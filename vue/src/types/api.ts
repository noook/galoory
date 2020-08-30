export type PaginationInterface<T> = {
  results: T[];
  pagination: {
    currentPage: number;
    maxPage: number;
    perPage: number;
    total: number;
  };
}
