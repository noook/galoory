class RouteCollection {
  public get(name: string, params: Record<string, string | number> = {}) {
    return this.replaceParams(name, params).replace(/\/\//g, '/');
  }

  private replaceParams(route: string, params: Record<string, number | string>) {
    return Object.entries(params).reduce((acc, [key, value]) => {
      return acc.replace(`{:${key}}`, value.toString());
    }, route);
  }
}

export const routeMap = new RouteCollection();
