import { AxiosInstance, AxiosResponse } from "axios";

export default class AbstractResource<C = any> {
    
    constructor(protected http: AxiosInstance, protected resource: string) { }

    list<T = {data: C[]}>(): Promise<AxiosResponse<T>> {
        return this.http.get<T>(this.resource);
    }
    get<T = C>(id: any): Promise<AxiosResponse<T>>{
        return this.http.get<T>(`${this.resource}/${id}`);
    }
    create<T = C>(data: any): Promise<AxiosResponse<T>>{
        return this.http.post<T>(this.resource, data);
    }
    update<T = C>(id: any, data: any): Promise<AxiosResponse<T>> {
        return this.http.put<T>(`${this.resource}/${id}`, data);
    }
    delete<T = C>(id: any): Promise<AxiosResponse<T>> {
        return this.http.delete(`${this.resource}/${id}`);
    }
}