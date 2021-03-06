import axios, { AxiosInstance, AxiosResponse, CancelTokenSource } from "axios";
export interface httpResponse<T> {
  data: T;
  links?: {
    first: string;
    last: string;
    prev?: string | null;
    next?: string | null;
  };
  meta?: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}
export default class AbstractResource<C = any> {
  private cancelList: CancelTokenSource | null = null;

  constructor(protected http: AxiosInstance, protected resource: string) {}

  list<T = httpResponse<C[]>>(params: {} = {}): Promise<AxiosResponse<T>> {
    if (this.cancelList) {
      this.cancelList.cancel("List request cancelled");
    }
    this.cancelList = axios.CancelToken.source();
    return this.http.get<T>(this.resource, {
      cancelToken: this.cancelList.token,
      params,
    });
  }
  get<T = httpResponse<C>>(id: any): Promise<AxiosResponse<T>> {
    return this.http.get<T>(`${this.resource}/${id}`);
  }
  create<T = httpResponse<C>>(data: any): Promise<AxiosResponse<T>> {
    return this.http.post<T>(this.resource, data);
  }
  update<T = httpResponse<C>>(id: any, data: any): Promise<AxiosResponse<T>> {
    return this.http.put<T>(`${this.resource}/${id}`, data);
  }
  delete<T = httpResponse<C>>(id: any): Promise<AxiosResponse<T>> {
    return this.http.delete(`${this.resource}/${id}`);
  }

  isCancel(error: any) {
    return axios.isCancel(error);
  }
}
