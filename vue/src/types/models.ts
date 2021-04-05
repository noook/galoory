export interface User {
  id: string;
  email: string;
  roles: string[];
  firstname: string;
  lastname: string;
}

type PhotoshootStatus = 'pending' | 'done';

export interface NewPhotoshoot {
  user: Pick<User, 'firstname' | 'email'>;
  date: Date;
  quantity: number;
  comment: string;
}

export interface PhotoshootDTO {
  id: string;
  customer: User;
  date: string;
  status: PhotoshootStatus;
  quantity: number;
  comment: string;
}

export interface Photoshoot {
  id: string;
  customer: User;
  date: Date;
  status: PhotoshootStatus;
  quantity: number;
  comment: string;
}

export interface NewSelectedPicture {
  id: string;
  filename: string;
}
